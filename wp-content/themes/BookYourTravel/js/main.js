// Lấy danh sách tất cả các item có sub-menu
const itemsWithSubMenu = document.querySelectorAll(".menu-item-has-children");

// Gắn sự kiện click cho từng item
itemsWithSubMenu.forEach((item) => {
  item.addEventListener("click", (event) => {
    // event.preventDefault(); // Ngăn chặn hành vi mặc định khi click vào item

    // Ẩn tất cả sub-menu đang hiển thị
    // hideAllSubMenus();

    // Hiển thị sub-menu tương ứng với item được click
    const subMenu = item.querySelector(".sub-menu");
    if (subMenu) {
      subMenu.classList.add("active-sub-menu"); // Thêm lớp active
    }
  });
});
// Gắn sự kiện click cho nút back
const backButton = document.querySelector(".back-btn");
backButton.addEventListener("click", () => {
  // Tìm sub-menu đang hiển thị
  const visibleSubMenu = document.querySelector(".sub-menu.active-sub-menu");
  if (visibleSubMenu) {
    visibleSubMenu.classList.remove("active-sub-menu"); // Xóa lớp active

    // Ẩn tất cả sub-menu con của sub-menu hiện tại
    hideAllSubMenus(visibleSubMenu);

    // Tìm menu cha của sub-menu hiện tại
    const parentItem = visibleSubMenu.closest(".sub-menu").parentElement;
    if (parentItem && parentItem.classList.contains("menu-item-has-children")) {
      const parentSubMenu = parentItem.querySelector(".sub-menu");
      if (parentSubMenu) {
        parentSubMenu.classList.add("active-sub-menu");
      }
    }
  }
});

// Hàm ẩn tất cả sub-menu
function hideAllSubMenus(menu) {
  const subMenus = menu.querySelectorAll(".sub-menu");

  subMenus.forEach((subMenu) => {
    subMenu.classList.remove("active-sub-menu");
    hideAllSubMenus(subMenu); // Ẩn tất cả sub-menu con của sub-menu
  });
}

// Lấy tất cả các phần tử .mega-menu-item-has-children
const menuItemHasChildrenList = document.querySelectorAll(
  ".mega-menu-item-has-children"
);

// Lặp qua từng phần tử và thêm sự kiện hover bằng JavaScript
menuItemHasChildrenList.forEach((menuItemHasChildren) => {
  const subMenu = menuItemHasChildren.querySelector(".mega-sub-menu");

  menuItemHasChildren.addEventListener("mouseenter", () => {
    subMenu.classList.add("active-mega-sub-menu");
  });

  menuItemHasChildren.addEventListener("mouseleave", () => {
    subMenu.classList.remove("active-mega-sub-menu");
  });
});

(function ($) {
  $("#get-email").val("");
  $("#get-email").attr("placeholder", "Enter your email");
  $(".show-all-items").on("click", function () {
    $(".body-itinerary-item").slideToggle();
  });
  $(".circle-plus").on("click", function () {
    var parentItem = $(this).closest(".customizable_itinerary-item");
    var bodyItem = parentItem.find(".body-itinerary-item");
    $(this).toggleClass("opened");
    // bodyItem.toggleClass("active-customizable_itinerary");
    bodyItem.slideToggle();
  });

  $(".circle-plus-two").on("click", function () {
    $(this).toggleClass("opened");
    var bodyItem = parentItem.find(".body-itinerary-item");
    $(this).toggleClass("opened");
    // bodyItem.toggleClass("active-customizable_itinerary");
    bodyItem.slideToggle();
  });

  // toggle FAQ
  $(".faq_item-header").on("click", function () {
    var parentItem = $(this).closest(".faq_item");
    var bodyItem = parentItem.find(".faq_item-body");
    $(this).toggleClass("active-arrow");
    bodyItem.slideToggle();
  });
  // toggle FAQ

  // toggle search value
  $(".search-input").on("click", function () {
    $(".search-value").toggleClass("active-search-fields");
  });
  // toggle search value

  $(".hidden_gem-action-btn").on("click", function () {
    var hiddenContent = $(".hidden_gem-bottom-paragraph");
    hiddenContent.slideToggle();
    var currentState = $(this).data("sate");

    if (currentState === "more") {
      $(this).text("Show less");
      $(this).data("sate", "less");
    } else {
      $(this).text("Show more");
      $(this).data("sate", "more");
    }
  });

  $(".header-mobile .hamburger-lines").click(function (e) {
    $(".mobile-nav").addClass("active-mobile-nav");
  });

  // toggle tour nav list
  $(".single-tour-nav-container .hamburger-lines").on("click", function (e) {
    $(".nav-tour-list").slideToggle();
  });

  // toggle tour nav list

  $(".mobile-nav").on("click", function (event) {
    var target = $(event.target);

    if (target.closest(".nav-mobile-wrap").length === 0) {
      if ($(".active-mobile-nav").length > 0) {
        $(".active-mobile-nav")
          .not(".nav-mobile-wrap")
          .removeClass("active-mobile-nav")
          .find("*")
          .removeClass("active-mobile-nav");
      }
    }
  });
  $(".view-btn").click(function () {
    var aboutContent = $(this)
      .closest(".about-us-item")
      .find(".about-content-body");
    aboutContent.toggleClass("active-about-content");

    var currentState = $(this).data("state");

    if (currentState === "more") {
      $(this).text("View less");
      $(this).data("state", "less");
    } else {
      $(this).text("View more");
      $(this).data("state", "more");
    }
  });

  const firstParagraph = $("#firstChar").find("p").eq(0);
  const firstCharacter = firstParagraph.text().charAt(0);

  const wrappedContent = `<div class="squareBox">${firstCharacter}</div>${firstParagraph
    .text()
    .substring(1)}`;

  firstParagraph.html(wrappedContent);
 // slide gallery in tour list
 const sliders = document.querySelectorAll(".tour_item-gallery");

 sliders.forEach(function (slider) {
   const sliderWrap = slider.querySelector("#post-gallery");
   if (sliderWrap) {
     const sliderDots = slider.querySelector("#slider-dots");
     const prevButton = slider.querySelector(".slider-prev");
     const nextButton = slider.querySelector(".slider-next");
     const slides = slider.querySelectorAll(".gallery-item");
     const slideWidth = slides[0].offsetWidth;
     const itemCount = slides.length;

     let currentIndex = 0;

     if (currentIndex == 0) {
       prevButton.style.display = "none";
     }

     function moveToSlide(index) {
       if (index < 0 || index >= itemCount) {
         return;
       }

       sliderWrap.style.transform = `translateX(-${index * slideWidth}px)`;
       currentIndex = index;

       if (currentIndex == 0) {
         prevButton.style.display = "none";
       } else {
         prevButton.style.display = "block";
       }

       if (currentIndex == itemCount - 1) {
         nextButton.style.display = "none";
       } else {
         nextButton.style.display = "block";
       }

       // Highlight active dot
       const dots = sliderDots.querySelectorAll(".dot");
       dots.forEach((dot) => dot.classList.remove("active-dot"));
       dots[currentIndex].classList.add("active-dot");
     }

     prevButton.addEventListener("click", () => {
       moveToSlide(currentIndex - 1);
     });

     nextButton.addEventListener("click", () => {
       moveToSlide(currentIndex + 1);
     });

     // Move to slide when click on dot
     const dots = sliderDots.querySelectorAll(".dot");
     dots.forEach((dot) => {
       dot.addEventListener("click", () => {
         const index = parseInt(dot.getAttribute("data-index"));
         moveToSlide(index);
       });
     });
   }
 });
 // slide gallery in tour list

  // fixed navbar

  var navWrapper = $(".single-tour-nav-wraper");
  var navWrapperOffset = navWrapper.offset().top;
  console.log(navWrapper)
  $(window).scroll(function() {
    var body = $(".single-tour-body");
    var stop = body.offset().top;
    console.log(stop)
    var scrollTop = jQuery(this).scrollTop();
    if (scrollTop >= navWrapperOffset) {
      jQuery(".single-tour-nav-wraper").addClass("navbar-fixed-top");
    }
    if (scrollTop < stop) {
      jQuery(".single-tour-nav-wraper").removeClass("navbar-fixed-top");
    }
  });
  // fixed navbar

   // fixed navbar (cruise)

})(jQuery);
 
document.addEventListener("DOMContentLoaded", function () {


  const activeMapBtns = document.querySelectorAll(".tour_item-location");

  activeMapBtns.forEach(function (btn) {
    const map = btn.querySelector(".tour_itinerary");
    if (map) {
      const openBtn = btn.querySelector(".tour_item-location-sequence");
      openBtn.addEventListener("click", function () {
        map.classList.add("active-map");
      });
      const closeBtn = btn.querySelector(".map-icon");
      closeBtn.addEventListener("click", function () {
        map.classList.remove("active-map");
      });
    }
  });

  // tour detail slider
  const slideContainer = document.querySelector("#post-slider");
  const slide = document.querySelector(".slides");
  const nextBtn = document.getElementById("next-btn");
  const prevBtn = document.getElementById("prev-btn");
  const interval = 3000;

  let slides = document.querySelectorAll(".slide");
  let index = 1;
  let slideId;

  const firstClone = slides[0].cloneNode(true);
  const lastClone = slides[slides.length - 1].cloneNode(true);

  firstClone.id = "first-clone";
  lastClone.id = "last-clone";

  slide.append(firstClone);
  slide.prepend(lastClone);

  const slideWidth = slides[index].clientWidth;

  slide.style.transform = `translateX(${-slideWidth * index}px)`;

  var totalSlides = parseInt(
    document.getElementById("slide_total").textContent,
    10
  );
  function updateSlideStatus() {
    const currentSlide = index === 0 ? slides.length - 2 : index;
    const displayedSlide = currentSlide <= totalSlides ? currentSlide : 1;
    document.getElementById("slide_status").textContent =
      displayedSlide.toString();
  }

  const startSlide = () => {
    slideId = setInterval(() => {
      // moveToNextSlide();
    }, interval);

    slides[0].classList.add("active");
  };

  const getSlides = () => document.querySelectorAll(".slide");

  slide.addEventListener("transitionend", () => {
    slides = getSlides();
    if (slides[index].id === firstClone.id) {
      slide.style.transition = "none";
      index = 1;
      slide.style.transform = `translateX(${-slideWidth * index}px)`;
    }

    if (slides[index].id === lastClone.id) {
      slide.style.transition = "none";
      index = slides.length - 2;
      slide.style.transform = `translateX(${-slideWidth * index}px)`;
    }
  });

  const moveToNextSlide = () => {
    slides = getSlides();
    if (index >= slides.length - 1) return;
    slides[index].classList.remove("active");
    index++;
    slide.style.transition = ".7s ease-out";
    slide.style.transform = `translateX(${-slideWidth * index}px)`;
    // Add "active" class to the next slide
    if (index < slides.length - 1) {
      slides[index].classList.add("active");
    }
    updateSlideStatus();
  };

  const moveToPreviousSlide = () => {
    if (index <= 0) return;
    slides[index].classList.remove("active");
    index--;
    slide.style.transition = ".7s ease-out";
    slide.style.transform = `translateX(${-slideWidth * index}px)`;
    // Add "active" class to the next slide
    if (index > 0) {
      slides[index].classList.add("active");
    }
    updateSlideStatus();
  };

  // slideContainer.addEventListener('mouseenter', () => {
  //     clearInterval(slideId);
  // });

  // slideContainer.addEventListener('mouseleave', startSlide);
  nextBtn.addEventListener("click", moveToNextSlide);
  prevBtn.addEventListener("click", moveToPreviousSlide);

  window.onload = function () {
    // updateTotalSlideStatus(); // Cập nhật tổng số slide ban đầu
    startSlide(); // Bắt đầu chạy slide
  };
});


(function($) {

})(jQuery);
