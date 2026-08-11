window.addEventListener("DOMContentLoaded", function () {
  // ======= AOS Animations (if you're using AOS) =======
  if (typeof AOS !== "undefined") {
    AOS.init({
      duration: 1000,
      once: true
    });
  }

  // ======= Neon Button Hover Glow + Ripple Effect =======
  const neonButtons = document.querySelectorAll(".neon-btn");

  neonButtons.forEach(button => {
    button.addEventListener("mouseenter", () => {
      button.style.textShadow = "0 0 10px #00f0ff, 0 0 20px #39FF14";
    });

    button.addEventListener("mouseleave", () => {
      button.style.textShadow = "";
    });

    button.addEventListener("click", e => {
      const circle = document.createElement("span");
      circle.classList.add("ripple");
      const rect = button.getBoundingClientRect();
      circle.style.left = (e.clientX - rect.left) + "px";
      circle.style.top = (e.clientY - rect.top) + "px";
      button.appendChild(circle);
      setTimeout(() => circle.remove(), 600);
    });
  });

  // ======= Smooth Scroll for Anchor Links =======
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener("click", function (e) {
      const target = document.querySelector(this.getAttribute("href"));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: "smooth" });
      }
    });
  });

  // ======= Fade-in on Scroll =======
  function fadeInOnScroll() {
    const elements = document.querySelectorAll(".fade-in-on-scroll");
    const windowBottom = window.innerHeight + window.scrollY;

    elements.forEach(el => {
      if (windowBottom > el.offsetTop + 100) {
        el.style.opacity = 1;
        el.style.transform = "translateY(0)";
        el.style.transition = "opacity 1s ease-out, transform 1s ease-out";
      }
    });
  }
  window.addEventListener("scroll", fadeInOnScroll);
  fadeInOnScroll();

  // ======= Form: Password Match Validation =======
  const form = document.querySelector("form");
  if (form) {
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm_password");

    if (password && confirmPassword) {
      form.addEventListener("submit", function (e) {
        if (password.value !== confirmPassword.value) {
          alert("Passwords do not match!");
          e.preventDefault();
        }
      });
    }
  }

  // ======= Toggle Password Visibility =======
  window.togglePassword = function (fieldId, btn) {
    const field = document.getElementById(fieldId);
    if (field.type === "password") {
      field.type = "text";
      btn.textContent = "Hide";
    } else {
      field.type = "password";
      btn.textContent = "Show";
    }
  };

  // ======= Contact Number Validation =======
  const contactInput = document.getElementById("contact");
  if (form && contactInput) {
    form.addEventListener("submit", function (e) {
      const phoneRegex = /^[0-9]{7,15}$/;
      if (!phoneRegex.test(contactInput.value.trim())) {
        alert("Please enter a valid contact number (7 to 15 digits only).");
        contactInput.focus();
        e.preventDefault();
      }
    });
  }

  // ======= Image Preview (For submit_pet.php) =======
  const imageInput = document.getElementById("pet_image");
  const preview = document.getElementById("preview");

  if (imageInput && preview) {
    imageInput.addEventListener("change", function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          preview.src = e.target.result;
          preview.style.display = "block";
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // ======= Input Fade-in Animation =======
  const inputs = document.querySelectorAll("form input, form textarea, form select");
  inputs.forEach((el, i) => {
    el.style.opacity = 0;
    el.style.transform = "translateY(20px)";
    setTimeout(() => {
      el.style.transition = "all 0.6s ease";
      el.style.opacity = 1;
      el.style.transform = "translateY(0)";
    }, 100 * i);
  });

  console.log("All scripts loaded.");
});
