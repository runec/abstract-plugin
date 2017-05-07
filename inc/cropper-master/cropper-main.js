/*
jQuery(document).ready( function() {
  'use strict'

  //Constructor
  function CropAvatar(jQueryelement) {

  }

  CropAvatar.prototype = {
    constructor: CropAvatar,


  }
});
*/

jQuery.fn.center = function () {
    this.css("position","absolute");
    this.css("top", Math.max(0, ((jQuery(window).height() - jQuery(this).outerHeight()) / 2) +
                                                jQuery(window).scrollTop()) + "px");
    return this;
}

function overlay() {
	el = document.getElementById('overlay');
	el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
  jQuery('#overlay').center();
}

function showImageAndAddCropper() {
  var files = jQuery('.avatar-input').prop('files');
  var url = URL.createObjectURL(files[0]);
  jQuery('.avatar-wrapper').empty().html('<img src="'+url+'">');

  jQuery('.avatar-wrapper > img').cropper({
    aspectRatio: 100/150,
    strict: false,
    guides: false,
    highlight: false,
    dragCrop: false,
    modal: false,
    moveable: false,
    zoomable: false,
    cropBoxMovable: true,
    cropBoxResizable: true,

    built: function() {
      var cropboxData = jQuery(this).cropper('getCropBoxData');
      var imageData = jQuery(this).cropper('getImageData');
      jQuery('.avatar-wrapper').width(imageData.width);
      jQuery('.avatar-wrapper').height(imageData.height);
      jQuery('.overlay-content').width(jQuery('.avatar-wrapper').outerWidth());
      jQuery('.overlay-content').height(jQuery('.avatar-wrapper').outerHeight() + jQuery('.avatar-upload').outerHeight());
      //jQuery('#overlay').width(jQuery('.overlay-content').outerWidth());
      //jQuery('#overlay').height(jQuery('.overlay-content').outerHeight() + );
      jQuery('.avatar-wrapper > img').cropper('setCanvasData', imageData);
      jQuery('.avatar-wrapper > img').cropper('setCropBoxData', {
        left: cropboxData.left,
        top: cropboxData.top,
        width: 100,
        height: 150
      });
    }
  });
}

function getCroppedImage() {
  var canvas = jQuery('.avatar-wrapper > img').cropper('getCroppedCanvas', {width:100, height:150});
  overlay();
  jQuery('#abstract-photo-preview').empty().html(canvas);
}

function closeCropper() {
  overlay();
}

function appendImageToForm() {
  var canvas = jQuery('canvas')[0];
  if(canvas) {
    var data = canvas.toDataURL('image/jpeg');
    console.log(data);
    var input = jQuery("<input>")
               .attr("type", "hidden")
               .attr("name", "imagedata").val(data);
    jQuery('#abs_form').append(jQuery(input));
  }
}
