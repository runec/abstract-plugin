/**
 * Kevon Adonis
 * Copyright 2013
 * http://www.wpabstracts.com
 */

function wp_field_validate(itemId, error) {
    var currentError = false;
    var email_regex = /^.+@.+\..+$/;
        if(itemId !== undefined){
            itemId = itemId.replace('[]', '\\[\\]');
            var inputItem = jQuery('#' + itemId);
            if( inputItem.val() ==='' ) {
                inputItem.parent().addClass('has-error');
                currentError = true;
            }else if (itemId == 'abs_presenter_email' && !email_regex.exec(inputItem.val())) {
                inputItem.parent().addClass('has-error');
                currentError = true;
            }
            else if (itemId == 'title' && inputItem.val().length > 60) {
                inputItem.parent().addClass('has-error');
                currentError = true;
            }
            else{
                inputItem.parent().removeClass('has-error');
            }
        }
    return currentError + error;
}

/*
 * Allows multiple attachments to be added
 */
function wpabstracts_add_attachment() {
    var container = document.createElement('div');
    var input = document.createElement('input');
    input.setAttribute('type','file');
    input.setAttribute('name','attachments[]');
    container.appendChild(input);
    document.getElementById('wpabstract_form_attachments').appendChild(container);
}
/*
 * Allows multiple attachments to be removed
 */
function wpabstracts_remove_attachment(id){
    var attachment_id = id;
    jQuery("#manage_attachments").append('<input type="hidden" name=\"abs_remove_attachments[]\" value ="'+ attachment_id +'">');
    jQuery("#attachment_"+attachment_id).remove();
}
/*
 * Validates and submit the Add Abstracts form
 */
function wpabstracts_validateAbstract() {

  var imageChosen = appendImageToFormNew();

  if(!imageChosen) {
    alert("Der er ikke tilføjet et billede");
    return;
  }

    var errors = false;
    var id;
    var maxCount = 0;
    var content = null;
    var count = 0;

    var counterids = {
      'abstext': ['.abs-word-count', '.max-word-count'],
      'resumetext': ['.resume-word-count', '.max-resume-count'],
      'targetgroup': ['.targetgroup-word-count', '.max-targetgroup-count'],
      'comments' : ['.comment-word-count', '.max-comment-count']
    };

    for(var i = 0; i < tinyMCE.editors.length; i++) {
      id = tinyMCE.editors[i].id;
      content = jQuery.trim(tinyMCE.editors[i].getContent({format: 'text'}));
      // ZACHEDIT: Always initialize count to zero!
      count = 0;
      if(content.length > 0){
          count = content.length;
      }
      maxCount = jQuery(counterids[id][1]).text();
      if(count > maxCount){
          alert("Det maksimale antal tegn i mindst et af tekstfelterne er overskredet");
          return;
      }
      if(id == 'abstext') {
        var minCount = jQuery('.min-word-count').text();
        console.log({min: minCount, c : count, t : count < minCount});
        if(count < minCount) {
          alert("Abstractet skal indeholde mindst " + minCount + " tegn");
          return;
        }

      }
      else if(id == 'resumetext'  && count === 0) {
        alert('Resume er ikke udfyldt');
        return;
      }
      else if(id == 'targetgroup'  && count === 0) {
        alert('Beskrivelse af målgruppe er ikke udfyldt');
        return;
      }
    }

    /*
    if(tinyMCE.activeEditor !== null){
        content = jQuery.trim(tinyMCE.activeEditor.getContent());
    }else{
        content = jQuery.trim(jQuery('#abstext').val());
    }
    */


    jQuery("form#abs_form input[type=text], select").each(function(){
        id = jQuery(this).attr('ID');
        //if(id !== 'abs_presenter_linkedin') {
          errors = wp_field_validate(id, errors);
        //}
    });

    if(errors) {
        alert(front_ajax.fillin);
    } else {
        jQuery('#abs_form').submit();
    }

}
/*
 * Validates and submits the Add Review form
 */
function wpabstracts_validateReview() {
    var errors = false;
    if(!jQuery("input[name='abs_relevance']:checked").val()){
        errors = true;
        jQuery('#abs_relevance_error').addClass('bg-danger');
    }else{
        jQuery('#abs_relevance_error').removeClass('bg-danger');
    }
    if(!jQuery("input[name='abs_quality']:checked").val()){
        errors = true;
        jQuery('#abs_quality_error').addClass('bg-danger');
    }else{
        jQuery('#abs_quality_error').removeClass('bg-danger');
    }
    if(!jQuery("input[name='abs_status']:checked").val()){
        errors = true;
        jQuery('#abs_status_error').addClass('bg-danger');
    }else{
        jQuery('#abs_status_error').removeClass('bg-danger');
    }
    var content = null;
    if(tinyMCE.activeEditor !== null){
        content = jQuery.trim(tinyMCE.activeEditor.getContent({format: 'text'}));
    }else{
        content = jQuery.trim(jQuery('#abs_comments').val());
    }
    if(content.length < 1) {
        errors = true;
        jQuery('#abs_review_comments_error').addClass('bg-danger');
    }
    else {
        jQuery('#abs_review_comments_error').removeClass('bg-danger');
    }
    if(errors) {
        alert(front_ajax.fillin);
    } else {
        jQuery('#wpabs_review_form').submit();
    }

}
/*
 * Allows for multiple co-authors to be added
 */
function wpabstracts_add_coauthor(){
    var html = '<hr class="soften" /><div class="form-group">' +
        '<label class="control-label" for="abs_author[]">'+front_ajax.authorName+'</label>' +
        '<input class="form-control" type="text" name="abs_author[]" id="abs_author[]"/>' +
        '<label class="control-label" for="abs_author_email[]">'+front_ajax.authorEmail+'</label>' +
        '<input class="form-control" type="text" name="abs_author_email[]" id="abs_author_email[]" value="" />' +
        '<label class="control-label" for="abs_author_affiliation[]">'+front_ajax.affiliation+'</label>' +
        '<input class="form-control" type="text" name="abs_author_affiliation[]" id="abs_author_affiliation[]" value="" />' +
        '</div>';
    jQuery('#coauthors_table').append(html);
}

/*
 * Allows for multiple co-authors to be removed
 */
function wpabstracts_delete_coauthor(){
    if(jQuery("#coauthors_table div").length > 1){
        jQuery('#coauthors_table').find("div:last").remove();
    }
}
/*
 * Allows for multiple topics to be added
 */
function wpabstracts_add_topic(){
    var html = '<tr>' +
        '<td>'+ front_ajax.topic +'</td>' +
        '<td><input type="text" name="topics[]" id="topics[]"/></td>' +
        '</tr>';
    jQuery('#topics_table').append(html);
}

function wpabstracts_delete_abstract(id) {
    if(confirm(front_ajax.confirmdelete)) {
        location.href = "?task=delete&id="+id+"";
    }
}

function wpabstracts_delete_review(id) {
    if(confirm(front_ajax.confirmdeleteReview)) {
        location.href = "?task=delete&id="+id+"";
    }

}

function wpabstracts_getTopics(id){
    var data = {
        action: 'loadtopics',
        event_id: id
    };
    jQuery.post(front_ajax.ajaxurl, data)
    .done(function(data){
        jQuery("#abs_topic").html(data);
    });
}

function wpabstracts_updateWordCount(){
    var counterids = {
      'abstext': ['.abs-word-count', '.max-word-count'],
      'resumetext': ['.resume-word-count', '.max-resume-count'],
      'targetgroup': ['.targetgroup-word-count', '.max-targetgroup-count'],
      'comments' : ['.comment-word-count', '.max-comment-count']
    };
    var content = null;
    var notified = false;
    var count = 0;
    var counter;
    var maxCount;
    var id;

    for(var i = 0; i < tinyMCE.editors.length; i++) {
      id = tinyMCE.editors[i].id;
      counter = jQuery(counterids[id][0]);
      maxCount = jQuery(counterids[id][1]).text();
      content = jQuery.trim(tinyMCE.editors[i].getContent({format: 'text'}));

      if(content.length > 0){
          count = content.split(' ').length;
      }
      counter.text(count + '. ' + (maxCount - count) + ' words remaining');
      if(count > maxCount && !notified){
          counter.css('color', 'red');
      }else{
           counter.css('color', 'green');
      }
    }
}

function wpabstracts_updateCharacterCount(){
    var counterids = {
      'abstext': ['.abs-word-count', '.max-word-count'],
      'resumetext': ['.resume-word-count', '.max-resume-count'],
      'targetgroup': ['.targetgroup-word-count', '.max-targetgroup-count'],
      'comments' : ['.comment-word-count', '.max-comment-count']
    };
    var content = null;
    var notified = false;
    var count;
    var counter;
    var maxCount;
    var id;

    for(var i = 0; i < tinyMCE.editors.length; i++) {
      count = 0;
      id = tinyMCE.editors[i].id;
      counter = jQuery(counterids[id][0]);
      maxCount = jQuery(counterids[id][1]).text();
      content = jQuery.trim(tinyMCE.editors[i].getContent({format: 'text'}));

      if(content.length > 0){
          count = content.length;
      }
      counter.text(count + '. ' + (maxCount - count) + ' tegn tilbage');
      if(count > maxCount && !notified){
          counter.css('color', 'red');
      }else{
           counter.css('color', 'green');
      }
    }
}

function appendImageToFormNew() {
  var canvas = jQuery('canvas')[0];
  if (!canvas) return false;
  var data = canvas.toDataURL('image/png');
  var input = jQuery("<input>");
  input
    .attr("type", "hidden")
    .attr("name", "imagedata")
    .val(data);
  jQuery('#abs_form').append(input);
  if(!canvas) {
    return false;
  }
  else {
    return true;
  }
}

function sortAbstractsAlphabetically() {
  jQuery("#sortPriority").val("false");
  var divList = jQuery(".abstract-frontend-listing-item");
  divList.sort(function(a, b){
    var s1 = jQuery(a).data("abstract-title");
    var s2 = jQuery(b).data("abstract-title");
    if(s1 > s2) {
      return 1;
    } else {
      return -1;
    }
  });
  jQuery("#abstract-frontend-listing").html(divList);
}

function sortAbstractsPriorityAndAlphabetically() {

  if(jQuery("#sortPriority").val() == "false") {
    jQuery("#sortPriority").val("true");
    var divList = jQuery(".abstract-frontend-listing-item");
    console.log(divList);
    divList.sort(function(a, b){
      var s1 = jQuery(a).data("abstract-title");
      var p1 = parseInt(jQuery(a).data("abstract-priority"));
      var s2 = jQuery(b).data("abstract-title");
      var p2 = parseInt(jQuery(b).data("abstract-priority"));
      if(p1 == p2) {
        if(s1 > s2) {
          return 1;
        } else {
          return -1;
        }
      }
      else {
        return p1-p2;
      }
    });
    console.log("AFTER");
    console.log(divList);
    jQuery("#abstract-frontend-listing").html(divList);
  }

}
