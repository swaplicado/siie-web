function disable(btn) {
        // disable the button
        btn.disabled = true;
        // submit the form
        btn.form.submit();
    }

$('#itm_class').change(function(e) {
		var classid = e.target.value;
		$.get('/genders/' + classid +  '', function(data) {
      console.log(data);
			$('#children').empty();
			$.each(data, function(key, value) {
				var option = $("<option></option>")
	                  .attr("value", key)
	                  .text(value);

				$('#itm_type').append(option);
			});
		});
	});
