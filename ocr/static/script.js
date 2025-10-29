document.getElementById("fileInput").addEventListener("change", (e) => {
  const files = e.target.files;
  const preview = document.getElementById("previewContainer");
  preview.innerHTML = "";

  if (!files.length) return;

  for (const file of files) {
    const div = document.createElement("div");
    div.style.marginBottom = "10px";
    div.style.padding = "8px";
    div.style.borderBottom = "1px solid #ddd";

    if (file.type.startsWith("image/")) {
      const img = document.createElement("img");
      img.src = URL.createObjectURL(file);
      img.style.maxWidth = "150px";
      img.style.borderRadius = "6px";
      img.style.marginRight = "10px";
      div.appendChild(img);
    } else {
      div.innerHTML += `📄 ${file.name}`;
    }

    preview.appendChild(div);
  }
});

document.getElementById("uploadBtn").addEventListener("click", async () => {
  const files = document.getElementById("fileInput").files;
  if (!files.length) return alert("Please select at least one file.");

  const resultsContainer = document.getElementById("result");
  resultsContainer.textContent = "";
  const progressContainer = document.getElementById("progressContainer");
  const progressBar = document.getElementById("progressBar");
  const progressText = document.getElementById("progressText");
  const status = document.getElementById("status");

  status.textContent = "";
  progressBar.value = 0;
  progressText.textContent = "Starting upload...";
  progressContainer.style.display = "block";

  let combinedOutput = "";

  for (let i = 0; i < files.length; i++) {
    const file = files[i];
    progressText.textContent = `Uploading ${file.name} (${i + 1}/${files.length})...`;

    const formData = new FormData();
    formData.append("file", file);

    try {
      const res = await fetch("/upload", {
        method: "POST",
        body: formData
      });
      const data = await res.json();

      if (data.error) {
        combinedOutput += `❌ ${file.name}: ${data.error}\n\n`;
      } else {
        document.getElementById("fileType").textContent = data.file_type;
        document.getElementById("department").textContent = data.department;
        document.getElementById("category").textContent = data.category;

        combinedOutput += `✅ ${file.name}\n`;
        combinedOutput += `Type: ${data.file_type}\n`;
        combinedOutput += `Department: ${data.department}\n`;
        combinedOutput += `Category: ${data.category}\n\n`;
        combinedOutput += `Extracted Text:\n${data.text}\n`;
        combinedOutput += `\n-----------------------------\n\n`;
      }

      progressBar.value = ((i + 1) / files.length) * 100;

    } catch (err) {
      combinedOutput += `❌ ${file.name}: ${err.message}\n\n`;
    }
  }

  progressText.textContent = "All files processed!";
  status.textContent = "✅ Done!";
  resultsContainer.textContent = combinedOutput;
});
