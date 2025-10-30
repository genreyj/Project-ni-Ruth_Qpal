import sys, json, os, mimetypes

os.environ.setdefault("KMP_DUPLICATE_LIB_OK", "TRUE")

def get_env_engine():
    # "fast" favors tesseract first, then easyocr, then paddle
    return os.environ.get("OCR_ENGINE", "fast").lower()

def preprocess_image(img):
    # Lazy import heavy libs only here
    from PIL import Image
    import numpy as np, cv2
    cv_img = np.array(img.convert("RGB"))
    cv_img = cv2.cvtColor(cv_img, cv2.COLOR_RGB2GRAY)
    _, thresh = cv2.threshold(cv_img, 150, 255, cv2.THRESH_BINARY_INV)
    kernel = np.ones((1, 1), np.uint8)
    processed = cv2.morphologyEx(thresh, cv2.MORPH_OPEN, kernel)
    return Image.fromarray(cv2.cvtColor(processed, cv2.COLOR_GRAY2RGB))

_easy_reader = None
def get_easy_reader():
    global _easy_reader
    if _easy_reader is None:
        import easyocr
        _easy_reader = easyocr.Reader(['en'])
    return _easy_reader

_paddle_reader = None
def get_paddle_reader():
    global _paddle_reader
    if _paddle_reader is None:
        from paddleocr import PaddleOCR
        _paddle_reader = PaddleOCR(use_angle_cls=True, lang='en')
    return _paddle_reader

def run_ocr(img, engine=None):
    engine = (engine or get_env_engine())
    # Preprocess once
    img = preprocess_image(img)

    # Try Tesseract first (fast and lightweight)
    if engine in ("fast", "tesseract"):
        try:
            import pytesseract
            text = pytesseract.image_to_string(img)
            if text and text.strip():
                return text
        except Exception:
            pass  # fallback below

    # Fallback to EasyOCR (model loads once per process)
    try:
        import numpy as np
        res = get_easy_reader().readtext(np.array(img))
        text = " ".join([r[1] for r in res])
        if text and text.strip():
            return text
    except Exception:
        pass

    # Final fallback to PaddleOCR (heavier)
    try:
        temp = "temp.png"
        img.save(temp)
        results = get_paddle_reader().ocr(temp)
        try:
            os.remove(temp)
        except Exception:
            pass
        lines = [line[1][0] for block in (results or []) for line in block]
        return " ".join(lines)
    except Exception:
        return ""

def get_file_type(path):
    mime, _ = mimetypes.guess_type(path)
    if not mime: return "unknown"
    if mime.startswith("image"): return "image"
    if "pdf" in mime: return "pdf"
    if "word" in mime or path.lower().endswith((".doc", ".docx")): return "word"
    if "excel" in mime or path.lower().endswith((".xls", ".xlsx")): return "excel"
    return "unknown"

def extract_text(path, engine=None):
    engine = engine or get_env_engine()
    ftype = get_file_type(path)
    text = ""
    try:
        if ftype == "image":
            from PIL import Image
            img = Image.open(path)
            text = run_ocr(img, engine)
        elif ftype == "pdf":
            import pdfplumber
            # First pass: embedded text (very fast)
            with pdfplumber.open(path) as pdf:
                for p in pdf.pages:
                    page_text = p.extract_text()
                    if page_text: text += page_text + "\n"
            # Fallback: OCR only pages without text
            if not text.strip():
                from pdf2image import convert_from_path
                imgs = convert_from_path(path)
                for img in imgs:
                    text += run_ocr(img, engine) + "\n"
        elif ftype == "word":
            import docx
            doc = docx.Document(path)
            text = "\n".join(p.text for p in doc.paragraphs)
        elif ftype == "excel":
            import pandas as pd
            df = pd.read_excel(path)
            text = df.to_string(index=False)
    except Exception as e:
        return ftype, f"Error: {e}"
    return ftype, (text or "").strip()

def suggest_meta(text):
    text_lower = (text or "").lower()
    dept = "general"
    cat = "uncategorized"
    if any(w in text_lower for w in ["invoice", "payment", "tax", "budget"]): dept = "finance"
    elif any(w in text_lower for w in ["patient", "medical", "clinic", "diagnosis"]): dept = "clinic"
    elif any(w in text_lower for w in ["server", "software", "system", "it"]): dept = "it"
    elif any(w in text_lower for w in ["employee", "salary", "recruitment", "hr"]): dept = "hr"

    # Category classification (priority order)
    if any(k in text_lower for k in ["announcement", "announcements", "advisory", "notice", "bulletin"]):
        cat = "announcement"
    elif any(k in text_lower for k in ["memo", "memorandum"]):
        cat = "memo"
    elif any(k in text_lower for k in ["policy", "guideline", "procedure"]):
        cat = "policy"
    elif any(k in text_lower for k in ["invoice", "billing", "receipt"]):
        cat = "invoice"
    elif any(k in text_lower for k in ["report", "minutes", "summary"]):
        cat = "report"

    return dept, cat

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"error": "No file provided"}))
        sys.exit(1)
    path = sys.argv[1]
    ftype, text = extract_text(path)
    dept, cat = suggest_meta(text)
    print(json.dumps({
        "file_type": ftype,
        "text": (text or "")[:2000],
        "department": dept,
        "category": cat
    }))
