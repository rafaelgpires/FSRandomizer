/* Global *****************************************************************************************************/
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

/* Landing Page ***********************************************************************************************
 * No need to run a check on whether we're on the landing page, since jQuery does that check for us
 * the code will simply not run if the elements don't exist without throwing any errors
 */
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
$("#options").data({nsongs: 15, variance: 25, encore: 100, encorebonus: 10, superencore: 20, superencorebonus: 25, resetencores: false}); //Default
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
$("#optionMenu").find('input').change(function(){
	switch(this.id) {
		case 'nsongs': validateOption(this, validateInteger(this.value, 660, 1), false); break;
		case 'variance': validateOption(this, (validatePercentage(this.value, 100, 1) && validateVarianceSum(this.value, 'encorebonus') && validateVarianceSum(this.value, 'superencorebonus'))); break;
		case 'encore': case 'superencore': validateOption(this, validatePercentage(this.value)); break;
		case 'encorebonus': case 'superencorebonus': validateOption(this, (validatePercentage(this.value) && validateVarianceSum(this.value, 'variance'))); break;
		case 'resetencores': $("#options").data('resetencores', $(this).prop('checked')); updateDescription(); break;
	}
});

/* FCTracker **************************************************************************************************/
//Logout
try {
	//Probably wanna do this server-side instead
	if(logged) {
		$("#modalpass").remove();
		$(".navbar-brand").attr('href', '#logout').click(function(){
			$.ajax({
				url: "./",
				data: {UniqueID: ListID, output: "logout"},
				async: false,
			}); window.location = './';
		}).children("span").text(logged[1]);
	}
} catch {} //Just in case logged isn't set we don't wanna crash JS

//Password Modal
$("#modalpass").on('shown.bs.modal', function() { $("#passinput").focus().removeClass("is-invalid"); });
$("#passinput").on('keypress', function(e){ if(e.which === 13) { $("#submitpass").click(); e.preventDefault(); } });
$("#submitpass").click(function() {
	$.ajax({
		url: "./",
		dataType: "json",
		data: {UniqueID: ListID, output: "validatepass", pass: $("#passinput").val()},
		async: false,
		success: function(data) {
			if(!data) $("#passinput").removeClass("is-valid").addClass("is-invalid");
			else { 
				$("#passinput").removeClass("is-invalid").addClass("is-valid");
				location.reload();
			}
		}
	});
});

//Speed Modal
function updateSpeedAvg() {
	if($.isEmptyObject(speedArr)) var average = 100;
	else var average = Object.values(speedArr).reduce((s,v) => s+v) / Object.keys(speedArr).length;
	$("#disable_speed").text(parseInt(average).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "%");
}
$("[name='speed']").click(function(e) {
	if(logged && !e.ctrlKey) {
		$("#submitspeed").data('song', ($(this).parent().prev().children().data('count')));
		$("#modalspeed").modal('toggle');
		e.preventDefault();
	}
});
$("#modalspeed").on('shown.bs.modal', function() {
	var speedTR = $("[data-count='"+$("#submitspeed").data('song')+"']").parent().next().children();
	$("#speed").removeClass("is-invalid").val(speedTR.text());
	$("#speedproof").removeClass('is-invalid').val(speedTR.attr('href'));
});
$("#speed").add("#speedproof").change(function(){ $(this).removeClass('is-invalid'); }).on('keypress', function(e) { if(e.which == 13) { $("#submitspeed").click(); e.preventDefault(); } });
$("#submitspeed").click(function(){
	//Parse
	var song = $(this).data('song');
	var speed = $("#speed").val();
	var proof = $("#speedproof").val();
	var regexp = /\b((?:[a-z][\w-]+:(?:\/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'".,<>?«»“”‘’]))/i;
	speed = parseInt(speed.replace("%", ""));
	if(speed < 100 || speed > 999) { $("#speed").addClass('is-invalid'); return false; }
	if(proof && !proof.match(regexp)) { $("#speedproof").addClass('is-invalid'); return false; }
	
	//Update page: Speed data
	var speedTR = $("[data-count='"+song+"']").parent().next().children();
	if(proof) speedTR.attr('href', proof);
	speedTR.text(speed + "%");
	
	//Update page: Speed average
	if($("[data-count='"+song+"']").attr('class') == "FC") { 
		speedArr[(song-1)] = speed;
		updateSpeedAvg();
	}
	
	//Update database
	$.post('.?update', {UniqueID: ListID, name: 'speed', value: song, speed: speed, proof: proof});
	$("#modalspeed").modal('toggle');
});

//Score Modal
var prevscore = 0;
$("[name='score']").click(function(e) { 
	if(logged && !e.ctrlKey) {
		$("#submitscore").data('song', ($(this).parent().prev().prev().children().data('count')));
		$("#modalscore").modal('toggle');
		e.preventDefault();
	}
});
$("#modalscore").on('shown.bs.modal', function() {
	var scoreTR = $("[data-count='"+$("#submitscore").data('song')+"']").parent().next().next().children();
	prevscore = scoreTR.text().replace(/\.|,/g, "");
	$("#score").removeClass("is-invalid").val(prevscore);
	$("#scorescore").removeClass('is-invalid').val(scoreTR.attr('href'));
});
$("#score").add("#scoreproof").change(function(){ $(this).removeClass('is-invalid'); }).on('keypress', function(e) { if(e.which == 13) { $("#submitscore").click(); e.preventDefault(); } });
$("#submitscore").click(function(){
	//Parse
	var score = $("#score").val().replace(/\.|,/g, "");
	var proof = $("#scoreproof").val();
	var regexp = /\b((?:[a-z][\w-]+:(?:\/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'".,<>?«»“”‘’]))/i;
	if(score < 1 || score > 9999999) { $("#score").addClass('is-invalid'); return false; }
	if(proof && !proof.match(regexp)) { $("#scoreproof").addClass('is-invalid'); return false; }
	
	//Update page
	var scoreTR = $("[data-count='"+$(this).data('song')+"']").parent().next().next().children();
	if(proof) scoreTR.attr('href', proof);
	scoreTR.text(score.replace(/\B(?=(\d{3})+(?!\d))/g, ","));
	currentAcc = $("#disable_score").text().replace(/\.|,/g, "");
	newAcc = parseInt(currentAcc) - parseInt(prevscore) + parseInt(score);
	$("#disable_score").text(newAcc.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
	
	//Update database
	$.post('.?update', {UniqueID: ListID, name: 'score', value: $(this).data('song'), score: score, proof: proof});
	$("#modalscore").modal('toggle');
});

//Header: Edit Title/Description
$("#listname").add("#listdesc").click(function(){
	if(logged) {
		$(this).hide(); 
		$(($(this).data('show'))).show().focus();
	} else $("#modalpass").modal('toggle');
});
$("#inputName").add("#inputDesc").on('keypress', function(e){ if(e.which === 13) { e.preventDefault(); $(this).blur(); } }).focusout(function(){
	//Parse
	var value = $(this).val();
	var limit = ($(this).attr('id') == 'inputName') ? 14 : 46;
	if(value.length > 0 && value.length < limit && value.match(/^\w+([ -_]\w+)*$/)) {
		//It's valid, set it and update the DB
		$.post('./?update', {UniqueID: ListID, name: $(this).data('name'), value: $(this).val()});
		$(this).hide();
		$(($(this).data('show'))).text($(this).val()).show();
	} else {
		//It's not valid, ignore it and reset the value of the input before hiding it
		$(this).val($(($(this).data('show'))).text()).hide();
		$(($(this).data('show'))).show();
	}
});

//FC Tracker Enable/Disable
function toggleFCTracker(val) {
	if(logged) {
		$.ajax({
			url: './?update',
			type: 'POST',
			data: {UniqueID: ListID, name: 'fctracker', value: val},
			async: false
		}); location.reload();
	} else $("#modalpass").modal('toggle');
}
$("#enable_tracker").click(function(){ toggleFCTracker(1); });
$("#disable_tracker").click(function(){ toggleFCTracker(0); });

//Unlocker Enable/Disable
$("#unlocker").click(function() {
	if(logged) {
		$.ajax({
			url: '.?update',
			type: 'POST',
			data: {UniqueID: ListID, name: 'unlocker', value: !unlocker},
			async: false,
		}); location.reload();
	} else $("#modalpass").modal('toggle');
});

//Speed Enable/Disabled
function toggleSS(type, val) {
	if(logged) {
		$.ajax({
			url: './?update',
			type: 'POST',
			data: {UniqueID: ListID, name: type, value: val},
			async: false
		}); location.reload();
	} else $("#modalpass").modal('toggle');
}
$("#enable_speed").click(function(){ toggleSS('speeder', 1); });
$("#disable_speed").click(function() { toggleSS('speeder', 0); });
$("#enable_score").click(function() { toggleSS('scorer', 1); });
$("#disable_score").click(function() { toggleSS('scorer', 0); });

//FC Mark FCs
$(".NoFC").click(function() { updateFC(this, true); });
$(".FC").click(function() { updateFC(this, false); });
function updateFC(DOM, fc) {
	if(logged) {
		//Update FC count
		var fcname    = fc ? 'FC' : 'NoFC';
		var fccount   = parseInt($("#fccount").text());
		var songcount = $(DOM).data('count');
		$(DOM).off('click').click(function() { updateFC(this, !fc); });
		$(DOM).attr('class', fcname);
		fccount = fc ? (fccount + 1) : (fccount - 1);
		$("#fccount").text(fccount);

		//Update database
		$.post('./?update', {UniqueID: ListID, name: fcname, value: songcount});
		
		//Update difficulty color
		var difficulty = 0;
		for (const [key, value] of Object.entries(fcs2diff))
			if(fccount >= key)
				difficulty = value;
			
		$("#fccount").parent().attr('class', ("Diff" + difficulty));
		
		//Update unlocker
		if(unlocker && fc) {
			//Find the next unlockable
			var nextTR = $(DOM).parent().parent().nextAll('.d-none:first');
			console.log(nextTR);
			
			//Check if it's a song or a chapter
			if(nextTR.attr('name') == "chapter") {
				//It's a chapter, unlock every song in the new chapter that's not an encore
				do {
					if(nextTR.attr('name') != 'encore')
						nextTR.removeClass('d-none');
					else break; //Stop at an encore
				} while(nextTR = nextTR.next())
			} else { 
				//It's a song, unlock it if all previous songs in this chapter are FC'd
				var chapterFCd = true;
				nextTR.prevUntil('[name="chapter"]').each(function(){
					if($(this).children().eq(1).children().attr('class') == 'NoFC')
						chapterFCd = false;
				});
				if(chapterFCd) nextTR.removeClass('d-none');
			}
		}
		
		//Update speed average
		if(typeof speedArr !== 'undefined') {
			if(fc) speedArr[songcount-1] = parseInt($(DOM).parent().next().text().replace("%", ""));
			else delete speedArr[songcount-1];
			updateSpeedAvg();
		}
	} else $("#modalpass").modal('toggle');
}