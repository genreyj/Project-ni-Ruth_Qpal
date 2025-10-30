// Toggle dropdown
function toggleDropdown() {
  const dropdown = document.getElementById("userDropdown");
  if (dropdown) dropdown.classList.toggle("show");
}

// Close dropdown when clicking outside
window.addEventListener("click", function (event) {
  const dropdown = document.getElementById("userDropdown");
  const avatar = document.querySelector(".user-avatar");
  if (!dropdown || !avatar) return;
  // If click is neither on avatar nor inside dropdown, close it
  if (!avatar.contains(event.target) && !dropdown.contains(event.target)) {
    dropdown.classList.remove("show");
  }
});

function toggleSidebar() {
  const sidebar = document.getElementById("sidebar");
  const menuToggle = document.getElementById("menuToggle");
  if (!sidebar || !menuToggle) return;
  const menuIcon = menuToggle.querySelector("i");
  sidebar.classList.toggle("collapsed");
  if (menuIcon) {
    menuIcon.className = sidebar.classList.contains("collapsed")
      ? "bi bi-justify-left"
      : "bi bi-list";
  }
}

// Initialize sidebar toggle
document.addEventListener("DOMContentLoaded", function () {
  const menuToggle = document.getElementById("menuToggle");
  if (menuToggle) {
    menuToggle.addEventListener("click", toggleSidebar);
  }

  // Section navigation with optional in-section scroll
  document.querySelectorAll(".nav-link").forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      switchSection(this.getAttribute("data-section"), this.querySelector("span")?.textContent || "Dashboard");

      // Optional scroll target in the same page
      const scrollSel = this.getAttribute("data-scroll");
      if (scrollSel) {
        setTimeout(() => {
          const target = document.querySelector(scrollSel);
          if (target) target.scrollIntoView({ behavior: "smooth", block: "start" });
        }, 50);
      }
    });
  });

  // Quick jump buttons from dashboard (View All / Reports) with optional scroll
  document.querySelectorAll(".nav-jump").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      const targetSection = this.getAttribute("data-target-section");
      const navLink = document.querySelector(`.nav-link[data-section="${targetSection}"]`);
      const label = navLink?.querySelector("span")?.textContent || targetSection;
      switchSection(targetSection, label);

      const scrollSel = this.getAttribute("data-scroll");
      if (scrollSel) {
        setTimeout(() => {
          const target = document.querySelector(scrollSel);
          if (target) target.scrollIntoView({ behavior: "smooth", block: "start" });
        }, 50);
      }
    });
  });

  function switchSection(sectionId, label) {
    // Update active nav link
    document.querySelectorAll(".nav-link").forEach((nav) => nav.classList.remove("active"));
    const match = document.querySelector(`.nav-link[data-section="${sectionId}"]`);
    if (match) match.classList.add("active");

    // Show corresponding section
    document.querySelectorAll(".section").forEach((s) => s.classList.remove("active"));
    const target = document.getElementById(sectionId);
    if (target) target.classList.add("active");

    // Update page title
    const titleEl = document.querySelector(".dashboard-title");
    if (titleEl) titleEl.textContent = (label || "Dashboard").toUpperCase();
  }

  // Image error handling
  const profilePic = document.querySelector(".profile-pic");
  if (profilePic) {
    profilePic.addEventListener("error", function () {
      console.warn("Image failed to load:", this.src);
      this.style.display = "none";
    });
  }

  const analysisProgressText = document.getElementById("analysisUploadProgressText");
  if (analysisProgressText) analysisProgressText.textContent = "Awaiting upload...";
  const analyzeBtn = document.getElementById("analyzeBtn");
  if (analyzeBtn) {
    analyzeBtn.textContent = "Analyze";
    analyzeBtn.disabled = false;
  }
  const fileInput = document.getElementById("documentFile");
  if (fileInput) {
    fileInput.addEventListener("change", handleFileSelection);
    if (fileInput.files.length) handleFileSelection();
  }
  document.getElementById("addDocumentModal")?.addEventListener("hidden.bs.modal", resetUploadState);
});

const activityLog = [
  { user: "John Smith", action: "LOGIN", table: "users", time: "Mar 15, 2024 09:23 AM" },
  { user: "Sarah Johnson", action: "CREATE", table: "documents", time: "Mar 15, 2024 09:15 AM" },
  { user: "Michael Brown", action: "UPDATE", table: "users", time: "Mar 15, 2024 08:45 AM" },
  { user: "System", action: "BACKUP", table: "database", time: "Mar 15, 2024 08:30 AM" },
  { user: "Lisa Anderson", action: "DELETE", table: "documents", time: "Mar 15, 2024 08:12 AM" }
];

const reportData = [
  { id: "DOC-00123", title: "Quarterly Financial Report", department: "Finance", category: "report", uploader: "John Smith", date: "2024-03-14", status: "Active" },
  { id: "DOC-00122", title: "HR Policy Update", department: "Human Resources", category: "policy", uploader: "Sarah Johnson", date: "2024-03-13", status: "Active" },
  { id: "DOC-00145", title: "System Maintenance Notice", department: "IT", category: "announcement", uploader: "System", date: "2024-03-15", status: "Published" },
  { id: "DOC-00148", title: "Clinic Advisory", department: "Clinic", category: "announcement", uploader: "Clinic Admin", date: "2024-03-15", status: "Published" },
  { id: "DOC-00149", title: "Vendor Invoice #8842", department: "Finance", category: "invoice", uploader: "AP Clerk", date: "2024-03-12", status: "Filed" }
];

let docsData = [];
let templatesData = [];
let currentReportRows = [...reportData];
let analysisComplete = false;
let lastAnalysisResults = null;

const usersData = [
  { id: 1, name: "Admin System", active: true },
  { id: 2, name: "Sarah Johnson", active: true },
  { id: 3, name: "Michael Brown", active: true },
  { id: 4, name: "Lisa Anderson", active: false }
];

// ====== Dashboard stats ======
function getStats() {
  const documents = docsData.filter(d => !d.deleted).length;
  const departments = new Set(docsData.map(d => d.department)).size;
  const usersTotal = usersData.length;
  const usersActive = usersData.filter(u => u.active).length;
  return { documents, departments, "users-total": usersTotal, "users-active": usersActive };
}

function animateCounts() {
  const stats = getStats();
  Object.entries(stats).forEach(([key, target]) => {
    const el = document.querySelector(`[data-stat="${key}"]`);
    if (!el) return;
    let n = 0;
    const step = Math.max(1, Math.floor(target / 30));
    const t = setInterval(() => {
      n += step;
      if (n >= target) { n = target; clearInterval(t); }
      el.textContent = String(n);
    }, 20);
  });
}

// ====== Recent activities ======
function loadActivities() {
  const tbody = document.getElementById("recentActivitiesBody");
  if (!tbody) return;
  tbody.innerHTML = activityLog.map(a => `
    <tr>
      <td>${a.user}</td>
      <td><span class="badge ${badgeCls(a.action)}">${a.action}</span></td>
      <td>${a.table}</td>
      <td>${a.time}</td>
    </tr>
  `).join("");
}
function badgeCls(action) {
  switch ((action || "").toUpperCase()) {
    case "LOGIN": return "bg-success";
    case "CREATE": return "bg-primary";
    case "UPDATE": return "bg-warning";
    case "DELETE": return "bg-danger";
    case "BACKUP": return "bg-info";
    default: return "bg-secondary";
  }
}

// ====== Document Management data (demo) ======
docsData = [
  {
    id: "DOC-00123",
    title: "Quarterly Financial Report",
    department: "Finance",
    category: "report",
    uploader: "John Smith",
    date: "2024-03-14",
    status: "Active",
    archived: false,
    deleted: false,
    versions: [
      { ver: "1.2", date: "2024-03-14 10:05", author: "John Smith" },
      { ver: "1.1", date: "2024-03-13 13:22", author: "John Smith" }
    ],
    comments: [
      { user: "Sarah Johnson", text: "Please verify totals.", time: "2024-03-14 11:10" }
    ],
    shares: []
  },
  {
    id: "DOC-00122",
    title: "HR Policy Update",
    department: "Human Resources",
    category: "policy",
    uploader: "Sarah Johnson",
    date: "2024-03-13",
    status: "Published",
    archived: false,
    deleted: false,
    versions: [{ ver: "2.0", date: "2024-03-13 09:00", author: "Sarah Johnson" }],
    comments: [],
    shares: []
  },
  {
    id: "DOC-00149",
    title: "Vendor Invoice #8842",
    department: "Finance",
    category: "invoice",
    uploader: "AP Clerk",
    date: "2024-03-12",
    status: "Filed",
    archived: true,
    deleted: false,
    versions: [{ ver: "1.0", date: "2024-03-12 08:32", author: "AP Clerk" }],
    comments: [],
    shares: []
  }
];

templatesData = [
  { id: "TPL-INV-01", name: "Invoice Template (DOCX)", category: "invoice", ext: "docx" },
  { id: "TPL-RPT-01", name: "Report Template (PDF)", category: "report", ext: "pdf" },
  { id: "TPL-MEM-01", name: "Memo Template (DOCX)", category: "memo", ext: "docx" }
];

// Current DM view
let docView = "files"; // files | archive | trash
let selectedDocId = null;

// ====== Document Management rendering ======
function renderTemplates() {
  const wrap = document.getElementById("templatesList");
  if (!wrap) return;
  if (!templatesData.length) {
    wrap.innerHTML = '<div class="text-muted">No templates yet.</div>';
    return;
  }
  wrap.innerHTML = `
    <div class="d-flex flex-wrap gap-2">
      ${templatesData
        .map(
          (t) => `
        <div class="badge bg-light text-dark border">
          <i class="bi bi-file-earmark me-1"></i>${t.name}
          <button class="btn btn-sm btn-link ms-2" onclick="downloadTemplate('${t.id}')">
            <i class="bi bi-download"></i>
          </button>
        </div>`
        )
        .join("")}
    </div>
  `;
}

function downloadTemplate(tid) {
  const t = templatesData.find((x) => x.id === tid);
  if (!t) return;
  // Demo content
  const content = `Template: ${t.name}\nCategory: ${t.category}\n\nUse this as a starting point.`;
  const blob = new Blob([content], { type: "text/plain;charset=utf-8" });
  const a = document.createElement("a");
  a.href = URL.createObjectURL(blob);
  a.download = `${t.name.replace(/\s+/g, "_")}.txt`;
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(a.href);
}

function renderDocsTable() {
  const tbody = document.getElementById("documentsManageTableBody");
  if (!tbody) return;

  // Counters
  const countFiles = document.getElementById("countFiles");
  const countArchive = document.getElementById("countArchive");
  const countTrash = document.getElementById("countTrash");
  if (countFiles) countFiles.textContent = docsData.filter((d) => !d.archived && !d.deleted).length;
  if (countArchive) countArchive.textContent = docsData.filter((d) => d.archived && !d.deleted).length;
  if (countTrash) countTrash.textContent = docsData.filter((d) => d.deleted).length;

  // Read filters
  const q = (document.getElementById("docSearch")?.value || "").toLowerCase();
  const dep = document.getElementById("docFilterDepartment")?.value || "";
  const cat = document.getElementById("docFilterCategory")?.value || "";
  const st = document.getElementById("docFilterStatus")?.value || "";
  const sortBy = document.getElementById("docSortBy")?.value || "recent";

  let rows = docsData.filter((d) => {
    if (docView === "files" && (d.archived || d.deleted)) return false;
    if (docView === "archive" && (!d.archived || d.deleted)) return false;
    if (docView === "trash" && !d.deleted) return false;

    const matchQ =
      !q ||
      d.id.toLowerCase().includes(q) ||
      d.title.toLowerCase().includes(q) ||
      (d.uploader || "").toLowerCase().includes(q);
    const matchDep = !dep || d.department === dep;
    const matchCat = !cat || d.category === cat;
    const matchSt = !st || d.status === st;
    return matchQ && matchDep && matchCat && matchSt;
  });

  rows.sort((a, b) => {
    if (sortBy === "title") return a.title.localeCompare(b.title);
    const ta = new Date(a.date).getTime();
    const tb = new Date(b.date).getTime();
    return sortBy === "oldest" ? ta - tb : tb - ta;
  });

  tbody.innerHTML = rows
    .map((r) => {
      const badge =
        r.status === "Active"
          ? "bg-success"
          : r.status === "Published"
          ? "bg-primary"
          : "bg-secondary";
      const actions = renderDocActions(r);
      return `
      <tr>
        <td>${r.id}</td>
        <td>${r.title}</td>
        <td>${r.department}</td>
        <td>${r.category}</td>
        <td>${r.uploader}</td>
        <td>${r.date}</td>
        <td><span class="badge ${badge}">${r.status}</span></td>
        <td>${actions}</td>
      </tr>`;
    })
    .join("");
}

function renderDocActions(doc) {
  // Actions required:
  // Version control & restore, Annotations & comments, Document sharing, Archive/Unarchive, Trash/Restore
  const archiveBtn = !doc.archived && !doc.deleted
    ? `<button class="btn btn-outline-secondary btn-sm" onclick="archiveDoc('${doc.id}')"><i class="bi bi-archive"></i></button>`
    : doc.archived && !doc.deleted
    ? `<button class="btn btn-outline-secondary btn-sm" onclick="unarchiveDoc('${doc.id}')"><i class="bi bi-archive-fill"></i></button>`
    : "";

  const trashBtn = !doc.deleted
    ? `<button class="btn btn-outline-danger btn-sm" onclick="trashDoc('${doc.id}')"><i class="bi bi-trash"></i></button>`
    : `<button class="btn btn-outline-success btn-sm" onclick="restoreFromTrash('${doc.id}')"><i class="bi bi-arrow-counterclockwise"></i></button>`;

  return `
    <div class="btn-group" role="group">
      <button class="btn btn-outline-primary btn-sm" title="Versions" onclick="openVersions('${doc.id}')"><i class="bi bi-clock-history"></i></button>
      <button class="btn btn-outline-dark btn-sm" title="Annotate" onclick="openAnnotations('${doc.id}')"><i class="bi bi-chat-left-text"></i></button>
      <button class="btn btn-outline-success btn-sm" title="Share" onclick="openShare('${doc.id}')"><i class="bi bi-share"></i></button>
      ${archiveBtn}
      ${trashBtn}
    </div>
  `;
}

// ====== Document actions ======
function findDoc(id) {
  return docsData.find((d) => d.id === id);
}

function archiveDoc(id) {
  const d = findDoc(id);
  if (!d) return;
  d.archived = true;
  renderDocsTable();
}
function unarchiveDoc(id) {
  const d = findDoc(id);
  if (!d) return;
  d.archived = false;
  renderDocsTable();
}
function trashDoc(id) {
  const d = findDoc(id);
  if (!d) return;
  d.deleted = true;
  renderDocsTable();
}
function restoreFromTrash(id) {
  const d = findDoc(id);
  if (!d) return;
  d.deleted = false;
  renderDocsTable();
}

// Versions modal
function openVersions(id) {
  selectedDocId = id;
  const d = findDoc(id);
  if (!d) return;
  const body = document.getElementById("versionHistoryBody");
  if (!body) return;
  const currentVer = d.versions?.[0]?.ver || "1.0";
  body.innerHTML = `
    <div class="mb-2"><strong>Document:</strong> ${d.id} - ${d.title}</div>
    <div class="mb-3"><span class="badge bg-info">Current Version: ${currentVer}</span></div>
    <div class="list-group">
      ${d.versions
        .map(
          (v, idx) => `
        <div class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <div><strong>v${v.ver}</strong></div>
            <div class="small text-muted">${v.date} • ${v.author}</div>
          </div>
          <div>
            <button class="btn btn-sm btn-outline-primary" onclick="restoreVersion(${idx})">
              <i class="bi bi-arrow-counterclockwise me-1"></i>Restore
            </button>
          </div>
        </div>`
        )
        .join("")}
    </div>
  `;
  new bootstrap.Modal(document.getElementById("versionHistoryModal")).show();
}

function restoreVersion(idx) {
  const d = findDoc(selectedDocId);
  if (!d || !d.versions?.[idx]) return;
  // For demo: move selected version to front
  const v = d.versions.splice(idx, 1)[0];
  d.versions.unshift(v);
  alert(`Restored version v${v.ver} for ${d.id}.`);
  renderDocsTable();
}

// Annotations modal
function openAnnotations(id) {
  selectedDocId = id;
  const d = findDoc(id);
  if (!d) return;
  const list = document.getElementById("annotationsList");
  if (!list) return;
  list.innerHTML =
    (d.comments || [])
      .map(
        (c) => `
      <div class="border rounded p-2 mb-2">
        <div><strong>${c.user}</strong> <span class="text-muted small">${c.time}</span></div>
        <div>${c.text}</div>
      </div>`
      )
      .join("") || '<div class="text-muted">No comments yet.</div>';
  document.getElementById("annotationInput").value = "";
  new bootstrap.Modal(document.getElementById("annotationsModal")).show();
}

document.addEventListener("click", function (e) {
  if (e.target && e.target.id === "addAnnotationBtn") {
    const input = document.getElementById("annotationInput");
    const txt = (input?.value || "").trim();
    if (!txt) return;
    const d = findDoc(selectedDocId);
    if (!d) return;
    d.comments = d.comments || [];
    const now = new Date();
    d.comments.push({
      user: "Admin System",
      text: txt,
      time: now.toISOString().slice(0, 16).replace("T", " ")
    });
    openAnnotations(selectedDocId);
  }
});

// Share modal
function openShare(id) {
  selectedDocId = id;
  const d = findDoc(id);
  if (!d) return;
  const link = `${location.origin}/ocr/share/${encodeURIComponent(d.id)}`;
  const linkEl = document.getElementById("shareLink");
  if (linkEl) linkEl.value = link;
  document.getElementById("shareEmail").value = "";
  document.getElementById("sharePermission").value = "view";
  document.getElementById("shareInfo").textContent = "";
  new bootstrap.Modal(document.getElementById("shareModal")).show();
}

document.addEventListener("click", function (e) {
  if (e.target && e.target.id === "copyShareLink") {
    const linkEl = document.getElementById("shareLink");
    const text = linkEl?.value || "";
    if (navigator.clipboard && text) {
      navigator.clipboard.writeText(text).catch(() => {
        linkEl?.select();
        document.execCommand("copy");
      });
    } else {
      linkEl?.select();
      document.execCommand("copy");
    }
  }
  if (e.target && e.target.id === "confirmShareBtn") {
    const email = (document.getElementById("shareEmail")?.value || "").trim();
    const perm = document.getElementById("sharePermission")?.value || "view";
    const d = findDoc(selectedDocId);
    if (!d || !email) return;
    d.shares = d.shares || [];
    d.shares.push({ email, permission: perm, at: new Date().toISOString() });
    const info = document.getElementById("shareInfo");
    if (info) info.textContent = `Shared with ${email} (${perm}).`;
  }
});

// ====== DM bindings ======
function bindDocumentManagement() {
  // Tabs
  document.querySelectorAll("#docTabs .nav-link").forEach((btn) => {
    btn.addEventListener("click", function () {
      document.querySelectorAll("#docTabs .nav-link").forEach((b) => b.classList.remove("active"));
      this.classList.add("active");
      docView = this.getAttribute("data-view") || "files";
      renderDocsTable();
    });
  });

  // Filters
  ["docSearch", "docFilterDepartment", "docFilterCategory", "docFilterStatus", "docSortBy"].forEach((id) => {
    const el = document.getElementById(id);
    if (!el) return;
    const evt = id === "docSearch" ? "input" : "change";
    el.addEventListener(evt, renderDocsTable);
  });

  // Initial render
  if (typeof renderTemplates === "function") renderTemplates();
  if (typeof renderDocsTable === "function") renderDocsTable();
}

// ====== Init ======
document.addEventListener("DOMContentLoaded", function () {
  loadActivities();
  animateCounts();
  renderReportTable(currentReportRows);
  document.getElementById("applyReportFilters")?.addEventListener("click", applyReportFilters);
  document.getElementById("resetReportFilters")?.addEventListener("click", resetReportFilters);
  document.getElementById("exportReportCsv")?.addEventListener("click", () => exportReportCSV("reportTable"));
  document.getElementById("exportReportExcel")?.addEventListener("click", () => exportReportExcel("reportTable"));
  document.getElementById("exportReportPdf")?.addEventListener("click", () => exportReportPDF("reportTable"));
  bindDocumentManagement();
});

// Expose fns for inline handlers
window.openVersions = window.openVersions || openVersions;
window.restoreVersion = window.restoreVersion || restoreVersion;
window.openAnnotations = window.openAnnotations || openAnnotations;
window.openShare = window.openShare || openShare;
window.archiveDoc = window.archiveDoc || archiveDoc;
window.unarchiveDoc = window.unarchiveDoc || unarchiveDoc;
window.trashDoc = window.trashDoc || trashDoc;
window.restoreFromTrash = window.restoreFromTrash || restoreFromTrash;
window.downloadTemplate = window.downloadTemplate || downloadTemplate;

document.getElementById("analyzeBtn").addEventListener("click", function () {
  if (analysisComplete) {
    handleUploadClick();
    return;
  }

  const fileInput = document.getElementById("documentFile");
  if (!fileInput || !fileInput.files.length) {
    alert("Please choose at least one file.");
    return;
  }

  analysisComplete = false;
  lastAnalysisResults = null;
  setAnalyzeButton("Analyzing...", true);

  const formData = new FormData();
  Array.from(fileInput.files).forEach((f) => formData.append("documentFile[]", f));

  const progressContainer = document.getElementById("progressContainer");
  const progressBar = document.getElementById("progressBar");
  const progressText = document.getElementById("progressText");
  const previewContainer = document.getElementById("previewContainer");
  const resultsContainer = document.getElementById("resultsContainer");
  const analysisUploadProgress = document.getElementById("analysisUploadProgress");
  const analysisUploadProgressBar = document.getElementById("analysisUploadProgressBar");
  const analysisUploadProgressText = document.getElementById("analysisUploadProgressText");

  if (progressContainer) progressContainer.style.display = "block";
  if (progressBar) progressBar.value = 10;
  if (progressText) progressText.textContent = "Preparing upload...";

  if (analysisUploadProgress && analysisUploadProgressBar) {
    analysisUploadProgressBar.style.width = "0%";
    analysisUploadProgressBar.textContent = "0%";
    analysisUploadProgressBar.setAttribute("aria-valuenow", "0");
  }
  if (analysisUploadProgressText) {
    analysisUploadProgressText.textContent = "Starting upload...";
  }

  const xhr = new XMLHttpRequest();
  xhr.open("POST", "/ocr/ocr_process.php", true);

  xhr.upload.onprogress = function (event) {
    if (!event.lengthComputable) return;
    const percent = Math.round((event.loaded / event.total) * 100);
    if (analysisUploadProgressBar) {
      analysisUploadProgressBar.style.width = percent + "%";
      analysisUploadProgressBar.textContent = percent + "%";
      analysisUploadProgressBar.setAttribute("aria-valuenow", String(percent));
    }
    if (analysisUploadProgressText) {
      analysisUploadProgressText.textContent = `Uploading ${percent}%`;
    }
    if (progressBar) progressBar.value = Math.max(progressBar.value, Math.min(90, percent));
  };

  xhr.onreadystatechange = function () {
    if (xhr.readyState !== XMLHttpRequest.DONE) return;

    if (analysisUploadProgressBar) {
      analysisUploadProgressBar.style.width = "100%";
      analysisUploadProgressBar.textContent = "100%";
      analysisUploadProgressBar.setAttribute("aria-valuenow", "100");
    }
    if (analysisUploadProgressText) {
      analysisUploadProgressText.textContent = xhr.status === 200 ? "Processing analysis..." : "Upload failed.";
    }
    if (progressBar) progressBar.value = 95;
    if (progressText) progressText.textContent = "Analyzing...";
  };

  xhr.onerror = function () {
    if (progressText) progressText.textContent = "Error during OCR.";
    if (analysisUploadProgressText) analysisUploadProgressText.textContent = "Upload failed.";
    analysisComplete = false;
    lastAnalysisResults = null;
    setAnalyzeButton("Analyze", false);
    alert("Error during OCR upload.");
  };

  xhr.onload = function () {
    const rawText = xhr.responseText || "";
    console.log("🔍 Raw OCR response:", rawText);
    let data;
    try {
      data = JSON.parse(rawText);
    } catch (err) {
      if (analysisUploadProgressText) analysisUploadProgressText.textContent = "Invalid server response.";
      analysisComplete = false;
      lastAnalysisResults = null;
      setAnalyzeButton("Analyze", false);
      alert("Invalid JSON response from server:\n\n" + rawText.slice(0, 500));
      return;
    }

    if (xhr.status !== 200) {
      const errMsg = data?.error || "Upload failed.";
      if (progressText) progressText.textContent = errMsg;
      analysisComplete = false;
      lastAnalysisResults = null;
      setAnalyzeButton("Analyze", false);
      alert(errMsg);
      return;
    }

    if (progressBar) progressBar.value = 100;
    if (progressText) progressText.textContent = "Analysis complete.";
    if (analysisUploadProgressText) analysisUploadProgressText.textContent = "Analysis complete.";

    const results = Array.isArray(data?.results) ? data.results : (data ? [data] : []);
    if (!results.length) {
      if (progressText) progressText.textContent = "No results returned from OCR.";
      analysisComplete = false;
      lastAnalysisResults = null;
      setAnalyzeButton("Analyze", false);
      alert("No results returned from OCR.");
      return;
    }

    if (previewContainer) {
      previewContainer.innerHTML = `
        <ul class="list-group">
          ${results.map((r, idx) => {
            const name = (r.original_name || `File ${idx + 1}`).replace(/</g, "&lt;").replace(/>/g, "&gt;");
            if (r.error) {
              return `<li class="list-group-item d-flex justify-content-between align-items-center">
                ${name}
                <span class="badge bg-danger">Error</span>
              </li>`;
            }
            const sizeLabel = formatBytes(r.file_size);
            return `<li class="list-group-item d-flex justify-content-between align-items-center">
              ${name}
              <span class="badge bg-secondary">${sizeLabel}</span>
            </li>`;
          }).join("")}
        </ul>
      `;
    }

    if (resultsContainer) {
      const tableHead = `
        <div class="table-responsive">
          <table class="table table-sm table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th style="min-width:220px;">File</th>
                <th>Type</th>
                <th>Department</th>
                <th>Category</th>
                <th style="width:140px;">Preview</th>
              </tr>
            </thead>
            <tbody>
      `;

      const tableRows = results.map((r, idx) => {
        const fileName = (r.original_name || `File ${idx + 1}`).replace(/</g, "&lt;").replace(/>/g, "&gt;");
        const type = (r.file_type || "unknown").replace(/</g, "&lt;").replace(/>/g, "&gt;");
        const dept = (r.department || "").replace(/</g, "&lt;").replace(/>/g, "&gt;");
        const cat = (r.category || "").replace(/</g, "&lt;").replace(/>/g, "&gt;");
        const url = r.public_url || "";
        const safeUrl = encodeURI(url).replace(/"/g, "%22").replace(/'/g, "%27");
        const rowId = `ocrPreviewRow_${idx}`;

        if (r.error) {
          const err = (r.error || "Unknown error").replace(/</g, "&lt;").replace(/>/g, "&gt;");
          return `
            <tr class="table-danger">
              <td>${fileName}</td>
              <td colspan="3">Error: ${err}</td>
              <td>-</td>
            </tr>
          `;
        }

        let previewHtml = "<em>(no preview available)</em>";
        const t = (r.file_type || "").toLowerCase();

        if (url) {
          if (t === "image") {
            previewHtml = `
              <img src="${safeUrl}" alt="${fileName}" class="img-fluid"
                   style="max-height:360px; width:auto; border:1px solid #eee; border-radius:4px;">
            `;
          } else if (t === "pdf") {
            previewHtml = `
              <embed src="${safeUrl}#toolbar=1&navpanes=0" type="application/pdf"
                     width="100%" height="420px" style="border:1px solid #eee; border-radius:4px;" />
            `;
          } else if (t === "word" || t === "excel") {
            previewHtml = `
              <div>
                <div class="mb-2 text-muted">Inline preview is not available for this file type.</div>
                <a class="btn btn-sm btn-outline-secondary" href="${safeUrl}" target="_blank" rel="noopener">
                  <i class="bi bi-box-arrow-up-right me-1"></i>Open File
                </a>
              </div>
            `;
          } else {
            previewHtml = `
              <a class="btn btn-sm btn-outline-secondary" href="${safeUrl}" target="_blank" rel="noopener">
                <i class="bi bi-box-arrow-up-right me-1"></i>Open File
              </a>
            `;
          }
        }

        return `
          <tr>
            <td>${fileName}</td>
            <td>${type}</td>
            <td>${dept || "-"}</td>
            <td>${cat || "-"}</td>
            <td>
              <button class="btn btn-sm btn-outline-primary" type="button"
                      data-bs-toggle="collapse" data-bs-target="#${rowId}"
                      aria-expanded="false" aria-controls="${rowId}">
                View Preview
              </button>
            </td>
          </tr>
          <tr class="collapse" id="${rowId}">
            <td colspan="5">
              <div class="card card-body" style="max-height: 460px; overflow: auto; white-space: normal;">
                ${previewHtml}
              </div>
            </td>
          </tr>
        `;
      }).join("");

      const tableFoot = `
            </tbody>
          </table>
        </div>
      `;

      resultsContainer.innerHTML = tableHead + tableRows + tableFoot;
    }

    lastAnalysisResults = results;
    analysisComplete = true;
    setAnalyzeButton("Upload", false);
  };

  xhr.send(formData);
});

function resetUploadState() {
  analysisComplete = false;
  lastAnalysisResults = null;
  setAnalyzeButton("Analyze", false);
  const progressBar = document.getElementById("progressBar");
  const progressText = document.getElementById("progressText");
  if (progressBar) progressBar.value = 0;
  if (progressText) progressText.textContent = "Awaiting upload...";
  document.getElementById("previewContainer")?.replaceChildren();
  document.getElementById("resultsContainer")?.replaceChildren();
}

function handleUploadClick() {
  if (!lastAnalysisResults?.length) {
    alert("Please analyze files before uploading.");
    return;
  }
  alert("Files are ready for upload. Implement the final submission here.");
}

function setAnalyzeButton(label, disabled) {
  const btn = document.getElementById("analyzeBtn");
  if (btn) {
    btn.textContent = label;
    btn.disabled = !!disabled;
  }
}

function handleFileSelection() {
  const fileInput = document.getElementById("documentFile");
  const files = Array.from(fileInput?.files || []);
  const previewContainer = document.getElementById("previewContainer");
  const progressBar = document.getElementById("progressBar");
  const progressText = document.getElementById("progressText");

  analysisComplete = false;
  lastAnalysisResults = null;
  setAnalyzeButton("Analyze", false);

  if (progressBar) progressBar.value = 0;
  if (progressText) progressText.textContent = files.length ? "Ready to analyze." : "Awaiting upload...";

  if (!previewContainer) return;
  if (!files.length) {
    previewContainer.innerHTML = '<div class="text-muted small">No files selected yet.</div>';
    return;
  }

  const items = files
    .map((file, idx) => {
      const safeName = (file.name || `File ${idx + 1}`).replace(/[&<>"']/g, (ch) => ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;"
      }[ch]));
      const sizeLabel = formatBytes(file.size);
      return `
        <li class="list-group-item d-flex justify-content-between align-items-center">
          ${safeName}
          <span class="badge bg-secondary">${sizeLabel}</span>
        </li>
      `;
    })
    .join("");

  previewContainer.innerHTML = `
    <div class="mb-2 text-muted small">Selected files (${files.length}):</div>
    <ul class="list-group">${items}</ul>
  `;
}

function formatBytes(bytes) {
  const n = Number(bytes);
  if (!Number.isFinite(n) || n < 0) return "n/a";
  if (n === 0) return "0 B";
  const units = ["B", "KB", "MB", "GB", "TB"];
  const idx = Math.min(units.length - 1, Math.floor(Math.log(n) / Math.log(1024)));
  const value = n / Math.pow(1024, idx);
  return `${value >= 100 ? Math.round(value) : value.toFixed(1)} ${units[idx]}`;
}
