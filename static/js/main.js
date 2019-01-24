
function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

$(document).ready(() => {



  $('.edit').click(function () {
    let form = $(this).siblings('.form')
    form.slideToggle();
  })

  $('#NewEntryButton').click(function () {
    let section = $('#NewEntryForm');
    $(this).fadeToggle(200);
    sleep(210).then( () => {
      section.fadeToggle(500);
    })
  })


})
