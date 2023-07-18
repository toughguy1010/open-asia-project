// const backButton = document.querySelector('.back-btn');
// const subMenuStack = []; // Mảng để lưu trạng thái submenu

// backButton.addEventListener('click', () => {
//   if (subMenuStack.length > 0) {
//     const currentSubMenu = subMenuStack.pop(); // Lấy submenu cuối cùng trong stack
//     const activeSubMenu = document.querySelector('.active-sub-menu');
//     if (activeSubMenu) {
//       activeSubMenu.classList.remove('active-sub-menu'); // Loại bỏ lớp "active-sub-menu" khỏi submenu hiện tại
//     }
//     currentSubMenu.classList.add('active-sub-menu'); // Hiển thị submenu trước đó (submenu thứ nhất)
//   }
// });

// const menuItems = document.querySelectorAll('.menu-item-has-children > a');

// menuItems.forEach((menuItem) => {
//   menuItem.addEventListener('click', (event) => {
//     event.preventDefault();

//     const submenu = menuItem.nextElementSibling;
//     if (submenu) {
//       submenu.classList.add('active-sub-menu'); // Hiển thị submenu khi click vào menu
//       subMenuStack.push(submenu); // Thêm submenu vào stack
//     }
//   });
// });

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

(function ($) {
  // $(window).scroll(function () {
  //   var navWrapper = $(".single-tour-nav-wraper");
  //   var navWrapperOffset = navWrapper.offset().top;
  //   var windowHeight = jQuery(window).height();
  //   var scrollTop = jQuery(this).scrollTop();
  //   var navWrapperHeight = navWrapper.outerHeight();
  //   console.log( navWrapperOffset + navWrapperHeight - windowHeight  )
  //   console.log('scrollTop', scrollTop  )
  //   console.log('navWrapperOffset', navWrapperOffset  )
  //   if (
  //     scrollTop >= navWrapperOffset &&
  //     scrollTop <= navWrapperOffset + navWrapperHeight - windowHeight
  //   ) {
  //     jQuery(".single-tour-nav-wraper").addClass("navbar-fixed-top");
  //   } else {
  //     jQuery(".single-tour-nav-wraper").removeClass("navbar-fixed-top");
  //   }

  //   if (scrollTop == 0) {
  //     jQuery(".single-tour-nav-wraper").removeClass("navbar-fixed-top");
  //   }
  // });

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
  $(".hidden_gem-action-btn").on("click", function () {
    var hiddenContent = $(".hidden_gem-bottom-paragraph");
    hiddenContent.slideToggle();
  });

  $(".hamburger-lines").click(function () {
    $(".mobile-nav").addClass("active-mobile-nav");
  });

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
  });

  const firstParagraph = $("#firstChar").find("p").eq(0);
  const firstCharacter = firstParagraph.text().charAt(0);

  const wrappedContent = `<div class="squareBox">${firstCharacter}</div>${firstParagraph
    .text()
    .substring(1)}`;

  firstParagraph.html(wrappedContent);
})(jQuery);
document.addEventListener("DOMContentLoaded", function () {
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
});
