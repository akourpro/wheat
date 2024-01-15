// language file
// languCookies = Cookies.get("lang");
// if (!languCookies) {
//   Cookies.set("lang", "ar", { secure: true, sameSite: "strict" });
//   languCookies = "ar";
// }
// if (languCookies == "en") {
//   languFile = "enjs.php";
// } else {
//   languFile = "arjs.php";
// }
// var langu;
// $.ajax({
//   url: "includes/lang/" + languFile,
//   dataType: "json",
//   async: false,
//   dataType: "json",
//   success: function (languSrc) {
//     langu = languSrc;
//   },
// });

// csrf
const csrf_token = $('meta[name="_csrf"]').attr("content");

// sweet alert
function sweet(type, title, text, link, close = false) {
  switch (type) {
    case "error":
      colorbtn = "#dc3545";
      break;
    case "success":
      colorbtn = "#218838";
      break;
    case "warning":
      colorbtn = "#ffc107";
      break;
    case "info":
      colorbtn = "#17a2b8";
      break;
    case "question":
      colorbtn = "#17a2b8";
      break;
    default:
      colorbtn = "#218838";
  }
  Swal.fire({
    icon: type,
    title: title,
    html: `` + text + ``,
    confirmButtonText: langu.ok,
    confirmButtonColor: colorbtn,
    showCloseButton: true,
  }).then((result) => {
    if (result.isConfirmed) {
      if (close) {
        window.close();
      } else {
        if (link == "here") {
          window.location = window.location.href;
        } else {
          window.location = link;
        }
      }
    } else {
      if (close) {
        window.close();
      } else {
        if (link == "here") {
          window.location = window.location.href;
        } else {
          window.location = link;
        }
      }
    }
  });
}
