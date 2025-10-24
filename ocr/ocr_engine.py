import os
import cv2
import pytesseract
from PIL import Image
import easyocr
from paddleocr import PaddleOCR
import pdfplumber
import docx
import pandas as pd
from pdf2image import convert_from_path
import numpy as np
import mimetypes

# Avoid MKL error
os.environ["KMP_DUPLICATE_LIB_OK"] = "TRUE"

# Initialize OCR engines
easyocr_reader = easyocr.Reader(['en'])
paddle_reader = PaddleOCR(use_textline_orientation=True, lang='en')


def preprocess_image(img: Image.Image) -> Image.Image:
    """Convert image to grayscale, threshold, and clean noise."""
    cv_img = np.array(img.convert("RGB"))
    cv_img = cv2.cvtColor(cv_img, cv2.COLOR_RGB2GRAY)
    _, thresh = cv2.threshold(cv_img, 150, 255, cv2.THRESH_BINARY_INV)
    kernel = np.ones((1, 1), np.uint8)
    processed = cv2.morphologyEx(thresh, cv2.MORPH_OPEN, kernel)
    return Image.fromarray(cv2.cvtColor(processed, cv2.COLOR_GRAY2RGB))


def run_ocr_on_image(img: Image.Image, engine: str = "tesseract") -> str:
    """Run OCR on a PIL image using the selected engine."""
    img = preprocess_image(img)
    if engine == "tesseract":
        return pytesseract.image_to_string(img)
    elif engine == "easyocr":
        results = easyocr_reader.readtext(np.array(img))
        return " ".join([res[1] for res in results])
    elif engine == "paddleocr":
        temp_path = "temp.png"
        img.save(temp_path, format="PNG")
        results = paddle_reader.ocr(temp_path)
        os.remove(temp_path)
        lines = []
        if results and len(results) > 0:
            for line in results[0]:
                text_part = line[1][0]
                lines.append(text_part)
        return " ".join(lines)
    else:
        raise ValueError("Unsupported OCR engine")


def get_file_type(file_path: str) -> str:
    """Detect file type by MIME or extension."""
    mime_type, _ = mimetypes.guess_type(file_path)
    if not mime_type:
        return "unknown"
    if mime_type.startswith("image"):
        return "image"
    elif mime_type == "application/pdf":
        return "pdf"
    elif "wordprocessingml.document" in mime_type:
        return "word"
    elif "excel" in mime_type:
        return "excel"
    else:
        return "unknown"


def auto_extract_text(file_path: str, engine: str = "easyocr") -> tuple[str, str]:
    """Extract text from PDF, Word, Excel, or image files."""
    file_type = get_file_type(file_path)
    text = ""

    if file_type == "image":
        img = Image.open(file_path)
        text = run_ocr_on_image(img, engine)

    elif file_type == "pdf":
        with pdfplumber.open(file_path) as pdf:
            for page in pdf.pages:
                page_text = page.extract_text()
                if page_text:
                    text += page_text + "\n"
        if not text.strip():  # fallback to OCR on PDF pages
            images = convert_from_path(file_path)
            for img in images:
                text += run_ocr_on_image(img, engine) + "\n"

    elif file_type == "word":
        doc = docx.Document(file_path)
        text = "\n".join([p.text for p in doc.paragraphs])

    elif file_type == "excel":
        df = pd.read_excel(file_path)
        text = df.to_string(index=False)

    else:
        raise ValueError(f"Unsupported or unknown file type: {file_path}")

    return file_type, text.strip() if text else ""


def auto_suggest_metadata(text: str) -> dict:
    """Suggest department and category based on extracted text keywords."""
    text_lower = text.lower()

    # Department keywords
    department_keywords = {
        "clinic": ["patient", "doctor", "medical", "appointment", "health", "diagnosis"],
        "finance": ["invoice", "budget", "payment", "balance", "receipt", "tax"],
        "hr": ["employee", "leave", "attendance", "salary", "recruitment"],
        "it": ["server", "network", "software", "system", "error", "support"],
        "admin": ["memo", "announcement", "meeting", "notice", "policy"],
    }

    # Category keywords
    category_keywords = {
        "memo": ["memo", "memorandum", "announcement"],
        "announcement": ["announcement", "notice", "update"],
        "patient record": ["patient", "record", "diagnosis", "treatment"],
        "invoice": ["invoice", "bill", "payment"],
        "report": ["report", "summary", "evaluation"],
        "policy": ["policy", "guidelines", "procedure"],
    }

    # Detect department
    detected_dept = "general"
    for dept, keywords in department_keywords.items():
        if any(word in text_lower for word in keywords):
            detected_dept = dept
            break

    # Detect category
    detected_cat = "uncategorized"
    for cat, keywords in category_keywords.items():
        if any(word in text_lower for word in keywords):
            detected_cat = cat
            break

    return {"department": detected_dept, "category": detected_cat}
