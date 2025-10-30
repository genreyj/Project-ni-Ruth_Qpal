document.getElementById("analyzeBtn").addEventListener("click", function () {
  const fileInput = document.getElementById("documentFile");
  if (!fileInput.files.length) {
    alert("Please choose a file first.");
    return;
  }

  const formData = new FormData();
  formData.append("documentFile", fileInput.files[0]);

  const progressBar = document.getElementById("progressBar");
  const progressContainer = document.getElementById("progressContainer");
  const progressText = document.getElementById("progressText");

  // show progress bar
  progressContainer.style.display = "block";
  progressBar.value = 30;
  progressText.textContent = "Uploading & analyzing...";

  fetch("http://localhost/ocr/ocr_process.php", {
    method: "POST",
    body: formData
  })
    .then(async (response) => {
      // Try to parse as JSON safely
      const text = await response.text();

      // For debugging (optional)
      console.log("Raw OCR Response:", text);

      let data;
      try {
        data = JSON.parse(text);
      } catch (e) {
        throw new Error("Invalid JSON returned from server:\n" + text.slice(0, 200));
      }

      if (data.error) {
        throw new Error(data.error);
      }

      progressBar.value = 100;
      progressText.textContent = "Analysis complete!";

      document.getElementById("ocrTextPreview").value = data.text || "";
      document.getElementById("detectedDepartment").value = data.department || "";
      document.getElementById("detectedCategory").value = data.category || "";
    })
    .catch((err) => {
      progressText.textContent = "Error during OCR.";
      alert("Error running OCR: " + err.message);
    });
});
