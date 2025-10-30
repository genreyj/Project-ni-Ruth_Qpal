from flask import Flask, request, jsonify
import os
import mimetypes

app = Flask(__name__)

UPLOAD_FOLDER = "uploads"
os.makedirs(UPLOAD_FOLDER, exist_ok=True)

_easy_reader = None
def get_easy():
    global _easy_reader
    if _easy_reader is None:
        import easyocr
        _easy_reader = easyocr.Reader(['en'])
    return _easy_reader

_paddle_reader = None
def get_paddle():
    global _paddle_reader
    if _paddle_reader is None:
        from paddleocr import PaddleOCR
        _paddle_reader = PaddleOCR(use_angle_cls=True, lang='en')
    return _paddle_reader

def get_file_type(path):
    mime, _ = mimetypes.guess_type(path)
    if not mime:
        return "unknown"
    if mime.startswith("image"):
        return "image"
    elif "pdf" in mime:
        return "pdf"
    elif "word" in mime or path.lower().endswith((".doc", ".docx")):
        return "word"
    elif "excel" in mime or path.lower().endswith((".xls", ".xlsx")):
        return "excel"
    else:
        return "unknown"

def ocr_image(path, engine="tesseract"):
    engine = (engine or "tesseract").lower()
    if engine in ("tesseract", "fast"):
        try:
            from PIL import Image
            import pytesseract
            img = Image.open(path)
            return pytesseract.image_to_string(img)
        except Exception:
            pass
    if engine in ("easyocr", "fast"):
        try:
            res = get_easy().readtext(path)
            return " ".join([r[1] for r in res])
        except Exception:
            pass
    # Final fallback
    try:
        results = get_paddle().ocr(path)
        return " ".join([line[1][0] for block in (results or []) for line in block])
    except Exception as e:
        return f"Error: {e}"

def extract_text(file_path, engine="tesseract"):
    file_type = get_file_type(file_path)
    text = ""
    if file_type == "image":
        text = ocr_image(file_path, engine=engine)
    elif file_type == "pdf":
        try:
            import pdfplumber
            with pdfplumber.open(file_path) as pdf:
                for page in pdf.pages:
                    t = page.extract_text()
                    if t:
                        text += t + "\n"
        except Exception:
            pass
        if not text.strip():
            try:
                from pdf2image import convert_from_path
                from PIL import Image
                pages = convert_from_path(file_path)
                for img in pages:
                    # Save to a temp file path to allow engines to consume file path
                    temp = os.path.join(UPLOAD_FOLDER, "_temp.png")
                    img.save(temp)
                    text += ocr_image(temp, engine=engine) + "\n"
                    if os.path.exists(temp):
                        os.remove(temp)
            except Exception as e:
                text = f"Error: {e}"
    elif file_type == "word":
        try:
            import docx
            doc = docx.Document(file_path)
            text = "\n".join([p.text for p in doc.paragraphs])
        except Exception as e:
            text = f"Error: {e}"
    elif file_type == "excel":
        try:
            import pandas as pd
            df = pd.read_excel(file_path)
            text = df.to_string(index=False)
        except Exception as e:
            text = f"Error: {e}"
    else:
        raise ValueError("Unsupported file type.")
    return file_type, (text or "").strip()

@app.route("/ocr", methods=["POST"])
def run_ocr():
    if "file" not in request.files:
        return jsonify({"error": "No file uploaded"}), 400

    engine = request.form.get("engine") or os.environ.get("OCR_ENGINE", "tesseract")
    file = request.files["file"]
    filename = file.filename
    path = os.path.join(UPLOAD_FOLDER, filename)
    file.save(path)

    try:
        file_type, text = extract_text(path, engine=engine)

        # Basic auto-classification
        tl = (text or "").lower()

        # Department detection (unchanged)
        department = "general"
        if any(k in tl for k in ["patient", "clinic", "medical", "diagnosis"]):
            department = "clinic"
        elif any(k in tl for k in ["invoice", "payment", "budget", "tax"]):
            department = "finance"
        elif any(k in tl for k in ["employee", "hr", "recruitment", "salary"]):
            department = "hr"

        # Category detection (new unified mapping)
        if any(k in tl for k in ["announcement", "announcements", "advisory", "notice", "bulletin"]):
            category = "announcement"
        elif any(k in tl for k in ["memo", "memorandum"]):
            category = "memo"
        elif any(k in tl for k in ["policy", "guideline", "procedure"]):
            category = "policy"
        elif any(k in tl for k in ["invoice", "billing", "receipt"]):
            category = "invoice"
        elif any(k in tl for k in ["report", "minutes", "summary"]):
            category = "report"
        else:
            category = "uncategorized"

        return jsonify({
            "file_type": file_type,
            "department": department,
            "category": category,
            "text": (text or "")[:2000]
        })

    except Exception as e:
        return jsonify({"error": str(e)}), 500
    finally:
        if os.path.exists(path):
            try:
                os.remove(path)
            except Exception:
                pass

if __name__ == "__main__":
    # Keep server lightweight at start; engines load on demand
    app.run(host="0.0.0.0", port=5000, debug=True)
