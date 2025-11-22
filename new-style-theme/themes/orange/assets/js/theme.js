
$(document).ready(function () {
    if (typeof notifications === "undefined") return;

    function showNotification(index) {
        if (index < notifications.length) {
            Swal.fire({
                title: notifications[index].title,
                html: notifications[index].text,
                icon: notifications[index].icon,
                showCancelButton: notifications[index].button ? true : false,
                confirmButtonText: "Close",
                cancelButtonText: notifications[index].button,
                reverseButtons: true,
                customClass: {
                    cancelButton: "orange-button-notifa"
                }
            }).then(function (result) {
                if (result.isConfirmed) {
                    var seenNotifications =
                        JSON.parse(localStorage.getItem("seenNotifications")) || [];
                    seenNotifications.push(notifications[index].id);
                    localStorage.setItem("seenNotifications", JSON.stringify(seenNotifications));

                    showNotification(index + 1);
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    window.open(notifications[index].url, "_blank");
                }
            });
        }
    }

    var seenNotifications = JSON.parse(localStorage.getItem("seenNotifications")) || [];

    var unseenNotifications = notifications.filter(function (notification) {
        return !seenNotifications.includes(notification.id);
    });

    if (unseenNotifications.length > 0) {
        notifications = unseenNotifications;
        showNotification(0);
    }
});



document.addEventListener('DOMContentLoaded', () => {
  // SMOOTH SLIDER FUNCTIONALITY
  const slider = document.querySelector('.icon-slider');
  let scrollInterval;

  function startAutoSlide() {
      scrollInterval = setInterval(() => {
          slider.scrollBy({
              left: 1, // Adjust the scrolling amount for smoothness
              behavior: "smooth", // Ensures smooth scrolling
          });

          // Reset scroll position if we reach the end
          if (slider.scrollLeft >= slider.scrollWidth - slider.clientWidth) {
              slider.scrollLeft = 0;
          }
      }, 10); // Adjust interval for smooth speed
  }

  if (slider) {
      // Start the auto slide on page load
      startAutoSlide();

      // Pause on hover
      slider.addEventListener('mouseover', () => clearInterval(scrollInterval));
      slider.addEventListener('mouseout', startAutoSlide);
  }

      // FAQ TOGGLE FUNCTIONALITY
    document.querySelectorAll('.faq-question').forEach((question) => {
      question.addEventListener('click', () => {
        const parent = question.parentElement;
    
        // Close all other open items
        document.querySelectorAll('.faq-item.open').forEach((item) => {
          if (item !== parent) {
            item.classList.remove('open');
          }
        });
    
        // Toggle current item
        parent.classList.toggle('open');
      });
    });


  // DROPDOWN FUNCTIONALITY
  const dropdownToggle = document.querySelector('.currency-dropdown .dropdown-toggle');
  const dropdownContainer = document.querySelector('.currency-dropdown');

  if (dropdownToggle && dropdownContainer) {
      dropdownToggle.addEventListener('click', (e) => {
          e.preventDefault();
          dropdownContainer.classList.toggle('dropdown-show'); // Use unique class
      });

      // Close dropdown if clicked outside
      document.addEventListener('click', (e) => {
          if (!dropdownContainer.contains(e.target)) {
              dropdownContainer.classList.remove('dropdown-show');
          }
      });
  }

  // MENU TOGGLE FUNCTIONALITY
  const toggleButton = document.querySelector('.menu-toggle');
  const menu = document.querySelector('.menu');

  if (toggleButton && menu) {
      toggleButton.addEventListener('click', () => {
          menu.classList.toggle('show');
          toggleButton.classList.toggle('up'); // Toggles arrow direction
      });
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const toggleButtons = document.querySelectorAll(".toggle-desc");

  toggleButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const index = this.getAttribute("data-index");

      // Close all other descriptions
      toggleButtons.forEach((btn) => {
        const otherIndex = btn.getAttribute("data-index");
        if (otherIndex !== index) {
          const otherShortDesc = document.getElementById(`short-${otherIndex}`);
          const otherFullDesc = document.getElementById(`full-${otherIndex}`);
          const otherButton = btn;

          // Reset other cards
          otherShortDesc.style.display = "block";
          otherFullDesc.style.display = "none";
          otherButton.textContent = "Read More";
        }
      });

      // Toggle current card
      const shortDesc = document.getElementById(`short-${index}`);
      const fullDesc = document.getElementById(`full-${index}`);
      if (fullDesc.style.display === "none") {
        fullDesc.style.display = "block";
        shortDesc.style.display = "none";
        this.textContent = "Read Less";
      } else {
        fullDesc.style.display = "none";
        shortDesc.style.display = "block";
        this.textContent = "Read More";
      }
    });
  });
});


document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelector('.slides');
    const navDots = document.querySelectorAll('.nav-dot');
    const prevBtn = document.getElementById('prev');
    const nextBtn = document.getElementById('next');

    let currentIndex = 0;
    let slideInterval;

    function updateSlider(index) {
        slides.style.transform = `translateX(-${index * 100}%)`;
        navDots.forEach(dot => dot.classList.remove('active'));
        if (navDots[index]) navDots[index].classList.add('active');
    }

    function nextSlide() {
        currentIndex = (currentIndex === navDots.length - 1) ? 0 : currentIndex + 1;
        updateSlider(currentIndex);
    }

    function startAutoSlide() {
        slideInterval = setInterval(nextSlide, 5000);
    }

    prevBtn.addEventListener('click', () => {
        currentIndex = (currentIndex === 0) ? navDots.length - 1 : currentIndex - 1;
        updateSlider(currentIndex);
        clearInterval(slideInterval);
        startAutoSlide();
    });

    nextBtn.addEventListener('click', () => {
        nextSlide();
        clearInterval(slideInterval);
        startAutoSlide();
    });

    navDots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            currentIndex = index;
            updateSlider(index);
            clearInterval(slideInterval);
            startAutoSlide();
        });
    });

    startAutoSlide();
});














