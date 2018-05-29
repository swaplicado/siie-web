function changeDate() {
    var oDate = document.getElementById('work_date').value;

    var data = { value : JSON.stringify(oDate) };
    $.ajax({
      type: "POST",
      url: '/siieweb/public/manage/changedate',
      data: data,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(data) {
         var serverData = JSON.parse(data);

         swal(serverData);
      }
    });
}
