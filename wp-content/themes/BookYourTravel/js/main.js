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
    console.log('84',menu)
  const subMenus = menu.querySelectorAll(".sub-menu");

  subMenus.forEach((subMenu) => {
    subMenu.classList.remove("active-sub-menu");
    hideAllSubMenus(subMenu); // Ẩn tất cả sub-menu con của sub-menu
  });
}

(function ($) {
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
})(jQuery);
