// ===========================
// File Viewer Modal
// ===========================

(function() {
  // Create modal HTML
  const modalHTML = `
    <div id="fileViewerModal" class="file-modal" style="display: none;">
      <div class="file-modal-overlay"></div>
      <div class="file-modal-content">
        <div class="file-modal-header">
          <h3 id="fileModalTitle">View Files</h3>
          <button class="file-modal-close" onclick="closeFileViewer()">&times;</button>
        </div>
        <div class="file-modal-body">
          <div id="filesGalleryContainer" style="display: none;">
            <div class="files-gallery-grid" id="filesGalleryGrid"></div>
          </div>
          <div id="imageViewerContainer" style="display: none;">
            <img id="modalImage" src="" alt="File preview">
            <div class="image-nav-buttons">
              <button class="nav-btn" id="prevImageBtn" onclick="navigateImage(-1)">❮</button>
              <button class="nav-btn" id="nextImageBtn" onclick="navigateImage(1)">❯</button>
            </div>
          </div>
          <div id="documentViewerContainer" style="display: none;">
            <iframe id="modalDocument" src="" frameborder="0"></iframe>
          </div>
        </div>
        <div class="file-modal-footer">
          <button class="btn secondary" id="backToGalleryBtn" onclick="backToGallery()" style="display: none;">← Back to Gallery</button>
          <button class="btn secondary" onclick="closeFileViewer()">Close</button>
          <a id="downloadFileBtn" href="" download class="btn primary" style="display: none;">Download</a>
        </div>
      </div>
    </div>
  `;

  // Inject modal into DOM when page loads
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      document.body.insertAdjacentHTML('beforeend', modalHTML);
    });
  } else {
    document.body.insertAdjacentHTML('beforeend', modalHTML);
  }

  // Global state for gallery mode
  let currentFiles = [];
  let currentFileIndex = 0;
  let isGalleryMode = false;

  // Global function to open files gallery (multiple files)
  window.openFilesGallery = function(filesArray) {
    currentFiles = filesArray;
    isGalleryMode = true;
    
    const modal = document.getElementById('fileViewerModal');
    const galleryContainer = document.getElementById('filesGalleryContainer');
    const imageContainer = document.getElementById('imageViewerContainer');
    const documentContainer = document.getElementById('documentViewerContainer');
    const galleryGrid = document.getElementById('filesGalleryGrid');
    const modalTitle = document.getElementById('fileModalTitle');
    const downloadBtn = document.getElementById('downloadFileBtn');
    const backBtn = document.getElementById('backToGalleryBtn');

    // Hide single file view
    imageContainer.style.display = 'none';
    documentContainer.style.display = 'none';
    downloadBtn.style.display = 'none';
    backBtn.style.display = 'none';

    // Show gallery
    galleryContainer.style.display = 'flex';
    modalTitle.textContent = `View Files (${filesArray.length})`;

    // Build gallery grid
    galleryGrid.innerHTML = '';
    filesArray.forEach((file, index) => {
      const filename = file.split('/').pop();
      const ext = filename.split('.').pop().toLowerCase();
      const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
      const isImage = imageExtensions.includes(ext);

      const fileCard = document.createElement('div');
      fileCard.className = 'file-card';
      fileCard.onclick = () => openFileFromGallery(index);

      if (isImage) {
        fileCard.innerHTML = `
          <div class="file-thumbnail">
            <img src="/PETVET/${file}" alt="${filename}">
          </div>
          <div class="file-name">${filename}</div>
          <div class="file-type">Image</div>
        `;
      } else {
        const icon = ext === 'pdf' ? '📄' : '📋';
        fileCard.innerHTML = `
          <div class="file-thumbnail document">
            <span class="file-icon">${icon}</span>
            <span class="file-ext">.${ext}</span>
          </div>
          <div class="file-name">${filename}</div>
          <div class="file-type">Document</div>
        `;
      }

      galleryGrid.appendChild(fileCard);
    });

    // Show modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  };

  // Open specific file from gallery
  window.openFileFromGallery = function(index) {
    currentFileIndex = index;
    const file = currentFiles[index];
    const filename = file.split('/').pop();
    
    openFileViewer('/PETVET/' + file, filename);
    
    // Show back button when viewing from gallery
    const backBtn = document.getElementById('backToGalleryBtn');
    backBtn.style.display = 'inline-block';
  };

  // Back to gallery view
  window.backToGallery = function() {
    openFilesGallery(currentFiles);
  };

  // Navigate between images (prev/next)
  window.navigateImage = function(direction) {
    if (!isGalleryMode) return;
    
    currentFileIndex += direction;
    if (currentFileIndex < 0) currentFileIndex = currentFiles.length - 1;
    if (currentFileIndex >= currentFiles.length) currentFileIndex = 0;
    
    openFileFromGallery(currentFileIndex);
  };

  // Global function to open file viewer
  window.openFileViewer = function(filePath, filename) {
    const modal = document.getElementById('fileViewerModal');
    const galleryContainer = document.getElementById('filesGalleryContainer');
    const imageContainer = document.getElementById('imageViewerContainer');
    const documentContainer = document.getElementById('documentViewerContainer');
    const modalImage = document.getElementById('modalImage');
    const modalDocument = document.getElementById('modalDocument');
    const modalTitle = document.getElementById('fileModalTitle');
    const downloadBtn = document.getElementById('downloadFileBtn');
    const prevBtn = document.getElementById('prevImageBtn');
    const nextBtn = document.getElementById('nextImageBtn');

    // Hide gallery
    galleryContainer.style.display = 'none';

    // Set filename in title
    modalTitle.textContent = filename || 'View File';

    // Set download link
    downloadBtn.href = filePath;
    downloadBtn.download = filename || '';
    downloadBtn.style.display = 'inline-block';

    // Determine file type
    const ext = filename.split('.').pop().toLowerCase();
    const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
    const isImage = imageExtensions.includes(ext);

    if (isImage) {
      // Show image viewer
      imageContainer.style.display = 'flex';
      documentContainer.style.display = 'none';
      modalImage.src = filePath;
      modalImage.alt = filename;
      
      // Show navigation if in gallery mode
      if (isGalleryMode && currentFiles.length > 1) {
        prevBtn.style.display = 'block';
        nextBtn.style.display = 'block';
      } else {
        prevBtn.style.display = 'none';
        nextBtn.style.display = 'none';
      }
    } else {
      // Show document viewer (PDF, DOC, etc.)
      imageContainer.style.display = 'none';
      documentContainer.style.display = 'flex';
      
      // For PDFs, use direct iframe. For other docs, might need Google Docs Viewer
      if (ext === 'pdf') {
        modalDocument.src = filePath;
      } else {
        // Use Google Docs Viewer for other document types
        modalDocument.src = `https://docs.google.com/viewer?url=${encodeURIComponent(window.location.origin + filePath)}&embedded=true`;
      }
    }

    // Show modal
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
  };

  // Global function to close file viewer
  window.closeFileViewer = function() {
    const modal = document.getElementById('fileViewerModal');
    const modalImage = document.getElementById('modalImage');
    const modalDocument = document.getElementById('modalDocument');

    // Hide modal
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';

    // Clear sources to stop loading
    modalImage.src = '';
    modalDocument.src = '';
    
    // Reset state
    currentFiles = [];
    currentFileIndex = 0;
    isGalleryMode = false;
  };

  // Close on overlay click
  document.addEventListener('click', (e) => {
    if (e.target.classList.contains('file-modal-overlay')) {
      closeFileViewer();
    }
  });

  // Close on Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      const modal = document.getElementById('fileViewerModal');
      if (modal && modal.style.display === 'flex') {
        closeFileViewer();
      }
    }
  });
})();
