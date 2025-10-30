// Contact Administrator Modal Function
function showContactModal() {
  const modalHtml = `
          <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header" style="background-color: var(--dms-light-blue);">
                  <h5 class="modal-title" id="contactModalLabel" style="color: var(--dms-dark-blue);">
                    <i class="bi bi-person-fill-gear me-2"></i>Contact Administrator
                  </h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <p><strong>To request access to the Document Management System:</strong></p>
                  <ul>
                    <li>Provide your Gmail address for account creation</li>
                    <li>Specify your required access level</li>
                    <li>Wait for account activation via email</li>
                  </ul>
                  <hr>
                  <p><strong>System Administrator:</strong></p>
                  <p><i class="bi bi-envelope-fill me-2"></i>genrey570@gmail.com</p>
                  <p><i class="bi bi-telephone-fill me-2"></i>09913910935</p>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-login" onclick="window.location.href='mailto:genrey570@gmail.com?subject=Document%20Management%20System%20Access%20Request'">
                    <i class="bi bi-envelope-fill me-2"></i>Send Email
                  </button>
                </div>
              </div>
            </div>
          </div>
        `;

  // Remove existing modal if any
  $("#contactModal").remove();

  // Add modal to body and show
  $("body").append(modalHtml);
  const modal = new bootstrap.Modal(document.getElementById("contactModal"));
  modal.show();
}

// Event listener for Contact Administrator button
document
  .getElementById("contactAdminBtn")
  .addEventListener("click", function (e) {
    e.preventDefault();
    showContactModal();
  });

// Event listener for Learn More button - scroll to Key Features
document
  .getElementById("getStartedBtn")
  .addEventListener("click", function (e) {
    e.preventDefault();

    // Scroll to Key Features section
    const keyFeaturesSection = document.getElementById("keyFeatures");
    const offsetTop = keyFeaturesSection.offsetTop - 100; // Account for fixed navbar

    window.scrollTo({
      top: offsetTop,
      behavior: "smooth",
    });
  });

// Smooth scrolling for Home button
document.getElementById("homeBtn").addEventListener("click", function (e) {
  e.preventDefault();

  // Scroll to the top of the page smoothly
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  });
});

// Smooth scrolling for Brand Container (Logo + Organization Name)
document
  .getElementById("brandContainer")
  .addEventListener("click", function (e) {
    e.preventDefault();

    // Scroll to the top of the page smoothly
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    });
  });

// Animation on scroll for fade-in elements
document.addEventListener("DOMContentLoaded", function () {
  const fadeElements = document.querySelectorAll(".fade-in");

  const fadeInOnScroll = function () {
    fadeElements.forEach((element) => {
      const elementTop = element.getBoundingClientRect().top;
      const elementVisible = 150;

      if (elementTop < window.innerHeight - elementVisible) {
        element.classList.add("active");
      }
    });
  };

  // Check on load
  fadeInOnScroll();

  // Check on scroll
  window.addEventListener("scroll", fadeInOnScroll);
});

// Smooth scrolling for anchor links
$('a[href^="#"]').on("click", function (e) {
  const target = $($(this).attr("href"));
  if (target.length) {
    e.preventDefault();
    $("html, body").animate(
      {
        scrollTop: target.offset().top - 70,
      },
      800
    );
  }
});

// Add loading animation
$(window).on("load", function () {
  $(".fade-in").first().addClass("active");
});

// Add keyboard navigation
$(document).on("keydown", function (e) {
  // Escape key closes any open modals
  if (e.keyCode === 27) {
    const modals = document.querySelectorAll(".modal.show");
    modals.forEach((modal) => {
      const modalInstance = bootstrap.Modal.getInstance(modal);
      if (modalInstance) {
        modalInstance.hide();
      }
    });
  }
});
