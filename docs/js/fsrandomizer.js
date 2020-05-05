//Alerts
function setAlert(id, message=null, type=null) {
	if(!message) { $('#'+id).hide(); }
	else {
		$("#"+id)
			.text(message)
			.attr('class', 'alert alert-' + type)
			.show();
	}
}

/* Landing Page
 * No need to run a check on whether we're on the landing page, since jQuery does that check for us
 * the code will simply not run if the elements don't exist without throwing any errors
 */
//NavBar: Input ID
$("#InputIDForm").submit(function(){
	//Check validity of the ID
	var continueSubmit = true;
	$.ajax({
		dataType: "json",
		data: {UniqueID: $("#InputID").val(), output: "validate"},
		async: false,
		success: function(data) {
			if(data) $("#InputID").removeClass("is-invalid").addClass("is-valid");
			else { 
				$("#InputID").removeClass("is-valid").addClass("is-invalid");
				continueSubmit = false;
			}
		}
	});
	
	//Either block the submit, or let it through
	return continueSubmit;
});

//Generator: Button
$("#generator").click(function(){
	//Let the user know it's loading
	setAlert('alert', 'Your list is being created...', 'info');
	
	//Generate a list with that ID
	var listGenerated = false;
	$.ajax({
		url: "?generate",
		type: "POST",
		dataType: "json",
		data: { options: function(){return JSON.stringify($("#options").data()); } },
		async: false,
		success: function(data) {
			//What to do after getting a reply
			if(data) {
				listGenerated = true;
				setAlert('alert', ('Your list has been created: ' + data + '!'), 'success');
				window.location = './' + data;
			}
		}
	});
	
	//If this runs, then there was an unknown error
	if(listGenerated == false) { setAlert('alert', 'Uh oh... There was an unknown error creating your list.', 'danger'); }
});

//Generator: Options - Validation
function validateInteger(x, l=1000, m=0) { return ($.isNumeric(x) && (x % 1 == 0) && x <= l && x >= m) ? true : false; }
function validatePercentage(x, l=100, m=0) { x = x.replace(/%/g, ""); return (validateInteger(x) && x <= l && x >= m) ? true : false; }
function validateVarianceSum(val, data) { return ((parseInt(val) + parseInt($("#options").data(data))) <= 100) ? true : false; }
function validateOption(DOM, valid, addPer=true) {
	if(!valid) $(DOM).removeClass('is-valid').addClass('is-invalid');
	else  {
		var val = $(DOM).val().replace(/%/g, "");
		$(DOM).removeClass('is-invalid').addClass('is-valid');
		$("#options").data(DOM.id, val);
		if(addPer) $(DOM).val(val + '%');
		updateDescription();
	}
}

//Generator: Options - Description
function updateInput() { for (const [key, value] of Object.entries($("#options").data())) $(("#"+key)).val(value + (key != 'nsongs' ? '%' : '')).removeClass('is-valid').removeClass('is-invalid'); }
function updateDescription() {
	var options = $("#options").data();
	var descriptors = $("#optionDesc").children('p');
	descriptors.eq(0).children('span').text((Math.ceil(660/options.nsongs)) + ' chapters');
	descriptors.eq(1).children('span').text(Math.floor(options.nsongs/5) + ' encores');
	descriptors.eq(2).children('span').text(options.variance + '% difficulty');
	descriptors.eq(3).children('span').eq(0).text(options.encore + '%');
	descriptors.eq(3).children('span').eq(1).text(options.encorebonus + '% harder');
	descriptors.eq(4).children('span').eq(0).text(options.superencore + '%');
	descriptors.eq(4).children('span').eq(1).text(options.superencorebonus + '% harder');
	descriptors.eq(5).children('span').text((options.resetencores ? 'Inconsistent' : 'Consistent'));
}

//Generator: Options - UI
$("#options").click(function(){ updateDescription(); updateInput(); $("#optionsMenu").modal('show'); });
$("#reset").click(function() { $("#options").data({nsongs: 15, variance: 25, encore: 100, encorebonus: 10, superencore: 20, superencorebonus: 25, resetencores: false}); updateDescription(); updateInput(); }).click();
$("#optionMenu").find('input').change(function(){
	switch(this.id) {
		case 'nsongs': validateOption(this, validateInteger(this.value, 660, 1), false); break;
		case 'variance': validateOption(this, (validatePercentage(this.value, 100, 1) && validateVarianceSum(this.value, 'encorebonus') && validateVarianceSum(this.value, 'superencorebonus'))); break;
		case 'encore': case 'superencore': validateOption(this, validatePercentage(this.value)); break;
		case 'encorebonus': case 'superencorebonus': validateOption(this, (validatePercentage(this.value) && validateVarianceSum(this.value, 'variance'))); break;
		case 'resetencores': $("#options").data('resetencores', $(this).prop('checked')); updateDescription(); break;
	}
});