from flask import Flask, render_template, request, jsonify
import os
from ocr_engine import auto_extract_text, auto_suggest_metadata  

app = Flask(__name__)

UPLOAD_FOLDER = os.path.join(os.getcwd(), "uploads")
os.makedirs(UPLOAD_FOLDER, exist_ok=True)


@app.route("/")
def index():
    return render_template("upload.html")


@app.route("/upload", methods=["POST"])
def upload_file():
    try:
        if "file" not in request.files:
            return jsonify({"error": "No file uploaded"}), 400

        file = request.files["file"]
        if file.filename == "":
            return jsonify({"error": "Empty filename"}), 400

        file_path = os.path.join(UPLOAD_FOLDER, file.filename)
        file.save(file_path)

        # ✅ Step 1: Run OCR
        file_type, text = auto_extract_text(file_path)

        # ✅ Step 2: Analyze extracted text for metadata
        metadata = auto_suggest_metadata(text)

        # ✅ Step 3: Return results to frontend
        return jsonify({
            "file_type": file_type,
            "department": metadata["department"],
            "category": metadata["category"],
            "text": text[:2000] if text else "(no text found)"
        })

    except Exception as e:
        import traceback
        print("❌ Server error:", traceback.format_exc())  # helpful debug
        return jsonify({"error": str(e)}), 500

    finally:
        if 'file_path' in locals() and os.path.exists(file_path):
            try:
                os.remove(file_path)
            except Exception:
                pass


if __name__ == "__main__":
    # ✅ Ensures debug reload works correctly
    app.run(host="0.0.0.0", port=5000, debug=True)
