/*jslint browser: true, devel: true, white: true, eqeq: true, plusplus: true, sloppy: true, vars: true*/
/*global $ */

/*************** General ***************/

var updateOutput = function (e) {
  var list = e.length ? e : $(e.target),
      output = list.data('output');
  if (window.JSON) {
    if (output) {
      output.val(window.JSON.stringify(list.nestable('serialize')));
    }
  } else {
    alert('JSON browser support required for this page.');
  }
};

var nestableList = $(".dd.nestable > .dd-list");

/***************************************/


/*************** Delete ***************/

var deleteFromMenuHelper = function (target) {
  /**
  if (target.data('new') == 1) {
    // if it's not yet saved in the database, just remove it from DOM
    target.fadeOut(function () {
      target.remove();
      updateOutput($('.dd.nestable').data('output', $('#json-output')));
    });
  } else {
    // otherwise hide and mark it for deletion
    target.appendTo(nestableList); // if children, move to the top level
    target.data('deleted', '1');
    target.fadeOut();
  }
  **/
  
  target.remove();
  updateOutput($('.dd.nestable').data('output', $('#json-output')));
  
};

var deleteFromMenu = function () {
  var targetId = $(this).data('owner-id');
  var target = $('[data-id="' + targetId + '"]');

  var result = confirm("Delete " + target.data('name') + " and all its subitems ?");
  if (!result) {
    return;
  }

  // Remove children (if any)
  target.find("li").each(function () {
    deleteFromMenuHelper($(this));
  });

  // Remove parent
  deleteFromMenuHelper(target);

  // update JSON
  updateOutput($('.dd.nestable').data('output', $('#json-output')));
};

/***************************************/


/*************** Edit ***************/

var menuAdd = $("#menu-add");
var menuEditor = $("#menu-editor");
var editButton = $("#editButton");
var editInputName = $("#editInputName");
var editInputSlug = $("#editInputSlug");
var editInputClass = $("#editInputClass");
var currentEditName = $("#currentEditName");

// Prepares and shows the Edit Form
var prepareEdit = function () {
  var targetId = $(this).data('owner-id');
  var target = $('[data-id="' + targetId + '"]');

  editInputName.val(target.data("name"));
  editInputSlug.val(target.data("slug"));
  editInputClass.val(target.data("class"));
  currentEditName.html(target.data("name"));
  editButton.data("owner-id", target.data("id"));

  console.log("[INFO] Editing Menu Item " + editButton.data("owner-id"));

  menuEditor.fadeIn('fast');
  menuAdd.fadeOut('fast');
};

// Edits the Menu item and hides the Edit Form
var editMenuItem = function () {
  var targetId = $(this).data('owner-id');
  var target = $('[data-id="' + targetId + '"]');

  var newName = editInputName.val();
  var newSlug = editInputSlug.val();
  var newClass = editInputClass.val();

  target.data("name", newName);
  target.data("slug", newSlug);
  target.data("class", newClass);

  target.find("> .dd-handle").html(newName);

  menuEditor.fadeOut('fast');
  menuAdd.fadeIn('fast');

  // update JSON
  updateOutput($('.dd.nestable').data('output', $('#json-output')));
};

/***************************************/


/*************** Add ***************/

var newIdCount =  new Date().getTime();

var addToMenu = function () {
  var newName = $("#addInputName").val();
  var newSlug = $("#addInputSlug").val();
  var newClass = $("#addInputClass").val();
  var newId = newIdCount;

  nestableList.append(
    '<li class="dd-item" ' +
    'data-class="' + newClass + '" ' +
    'data-id="' + newId + '" ' +
    'data-name="' + newName + '" ' +
    'data-slug="' + newSlug + '">' +
    '<div class="dd-handle">' + newName + '</div> ' +
    '<span class="button-delete button-delete-' + newId + ' btn btn-danger btn-xs" ' +
    'data-owner-id="' + newId + '"> ' +
    'Delete' +
    '</span>' +
    '<span class="button-edit button-edit-' + newId + ' btn btn-primary btn-xs" ' +
    'data-owner-id="' + newId + '">' +
    'Edit' +
    '</span>' +
    '</li>'
  );

  newIdCount++;

  // update JSON
  updateOutput($('.dd.nestable').data('output', $('#json-output')));

  // set events
  $(".dd.nestable .button-delete-"+newId).on("click", deleteFromMenu);
  $(".dd.nestable .button-edit-"+newId).on("click", prepareEdit);

  // clear input  
  $("#addInputName").val('');
  $("#addInputSlug").val('');
  $("#addInputClass").val('');

};



/***************************************/



$(function () {

  // output initial serialised data
  updateOutput($('.dd.nestable').data('output', $('#json-output')));

  // set onclick events
  editButton.on("click", editMenuItem);

  $(".dd.nestable .button-delete").on("click", deleteFromMenu);

  $(".dd.nestable .button-edit").on("click", prepareEdit);

  $("#menu-editor").submit(function (e) {
    e.preventDefault();
  });

  $("#menu-add").submit(function (e) {
    e.preventDefault();
    addToMenu();
  });

});

