<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Font Group System</title> 
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css"> 

    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>

<div class="container mt-5">

    <!-- Font Upload Section -->
    <div id="dropZone" class="p-5 text-center" style="cursor: pointer; border: 2px dashed #B2BEB5;">
        <img src="assets/upload_icon.png" id="uploadIcon" width="10%" height="10%"/>
        <input type="file" id="fontUpload" accept=".ttf" multiple style="display: none;" />
        <p><b>Click to upload</b> or drag and drop</p>
        <p>Only TTF File Allowed</p>
    </div>
    <hr>
        
    <!-- Uploaded Fonts List -->
    <h2>Our Fonts</h2>
    <p>Browse a list of Zepto fonts to build your font group.</p>
    <table class="table" style="border: none;">
        <thead>
            <tr style="background-color: #E0FFFF;">
                <th>FONT NAME</th>
                <th>PREVIEW</th> 
            </tr>
        </thead>
        <tbody id="fontList">
            <!-- Fonts will be dynamically loaded here -->
        </tbody>
    </table>
    <hr>

    <!-- Create Font Group -->
    <h2 id="formHeading">Create Font Group</h2>
    <p>You have to select at least two fonts</p>
    <form id="fontGroupForm">
        <!-- Added the missing group name input field -->
        <div class="form-group">
            <input type="text" name="group_name" class="form-control" placeholder="Group Name" required />
        </div>
        <div id="fontGroupFields">
            <div class="fontGroupRow">
                <div class="form-group">
                    <input type="text" name="custom_font_names[]" class="form-control" placeholder="Custom Font Name" required />
                </div>
                <div class="form-group">
                    <select name="fonts[]" class="form-control" required>
                        <option value="" disabled selected>Select Font</option>
                        <!-- Options populated by JavaScript -->
                    </select>
                </div>
                <div class="form-group">
                    <img src="assets/cross.png" alt="Remove" class="removeRow" />
                </div>
            </div>
        </div>
        <div class="form-actions mt-3">
            <button type="button" id="addRowButton" class="btn btn-add-row">+Add Row</button>
            <button type="submit" id="formSubmitButton" class="btn btn-create">Create</button>
        </div>
    </form>
    <hr>

    <!-- Font Groups List -->
    <h2>Font Groups</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>NAME</th>
                <th>FONTS</th>
                <th>COUNT</th>
            </tr>
        </thead>
        <tbody id="fontGroupList">
            <!-- Font groups will be dynamically loaded here -->
        </tbody>
    </table>

</div>

<!-- jQuery -->
<script src="js/jquery.min.js"></script>
<!-- Bootstrap JS -->
<script src="js/bootstrap.bundle.min.js"></script>

<!-- Main JavaScript -->
<script>
document.getElementById('dropZone').addEventListener('click', function() {
        document.getElementById('fontUpload').click();
    });

$(document).ready(function() {
    // Global variables for mode management
    var isEditMode = false;
    var editGroupId = null;

    // Function to load fonts
    function loadFonts(callback) {
        $.ajax({
            url: 'get_fonts.php',
            type: 'GET',
            dataType: 'json',
            success: function(fonts) {
                $('#fontList').empty();
                var fontOptions = '';
                $.each(fonts, function(index, font) {
                    // Dynamically add @font-face rule
                    var fontFace = `
                        @font-face {
                            font-family: 'font_${font.id}';
                            src: url('fonts/${font.file_name}');
                        }
                    `;
                    $('<style>').text(fontFace).appendTo('head');

                    // Display font with preview and delete button
                    var fontItem = `
                        <tr>
                            <td>${font.name}</td>
                            <td style="display: flex; justify-content: space-between; align-items: center;">
                                <p style="font-family: 'font_${font.id}'; font-size: 24px; margin: 0;">Example Style</p>
                                <span class="deleteFont" data-id="${font.id}" style="color: red; cursor: pointer;"><b>Delete</b></span>
                            </td>
                        </tr>
                    `;
                    $('#fontList').append(fontItem);

                    // Prepare options for select fields
                    fontOptions += `<option value="${font.id}">${font.name}</option>`;
                });

                // Populate select fields without resetting selected values
                $('select[name="fonts[]"]').each(function() {
                    var selectedValue = $(this).val();
                    $(this).html('<option value="" disabled>Select Font</option>' + fontOptions);
                    if (selectedValue) {
                        $(this).val(selectedValue);
                    }
                });

                // Callback after fonts are loaded
                if (callback) callback();
            }
        });
    }

    // Function to load font groups
    function loadFontGroups() {
        $.ajax({
            url: 'get_font_groups.php',
            type: 'GET',
            dataType: 'json',
            success: function(groups) {
                $('#fontGroupList').empty();
                $.each(groups, function(index, group) {
                    var groupItem = `
                        <tr>
                            <td>${group.name}</td>
                            <td>${group.fonts}</td>
                            <td style="display: flex; justify-content: space-between; align-items: center;">
                                <span>${group.count}</span>
                                <span style="display: flex; gap: 10px;">
                                    <span class="editGroup action-link" data-id="${group.id}" style="color: blue; cursor: pointer;">Edit</span>
                                    <span class="deleteGroup action-link" data-id="${group.id}" style="color: red; cursor: pointer;">Delete</span>
                                </span>
                            </td>
                        </tr>
                    `;
                    $('#fontGroupList').append(groupItem);
                });
            }
        });
    }

    // Load fonts and font groups on page load
    loadFonts();
    loadFontGroups();

    // Attach event handlers using event delegation
    $('#fontGroupList').off('click', '.editGroup').on('click', '.editGroup', function() {
        var groupId = $(this).data('id');
        editFontGroup(groupId);
    });

    $('#fontGroupList').off('click', '.deleteGroup').on('click', '.deleteGroup', function() {
        var groupId = $(this).data('id');
        if (confirm('Are you sure you want to delete this group?')) {
            $.ajax({
                url: 'delete_font_group.php',
                type: 'POST',
                data: { id: groupId },
                success: function(response) {
                    var res = JSON.parse(response);
                    alert(res.message);
                    if (res.status) {
                        loadFontGroups();
                        resetFontGroupForm();
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + error);
                }
            });
        }
    });

    // Handle drag and drop
    var dropZone = $('#dropZone');

    // Prevent default drag behaviors
    $(document).on('dragover drop', function(e) {
        e.preventDefault();
    });

    // Highlight drop zone on drag over
    dropZone.on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('hover');
    });

    dropZone.on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('hover');
    });

    // Handle file drop
    dropZone.on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('hover');

        var files = e.originalEvent.dataTransfer.files;
        handleFiles(files);
    });

    // Open file dialog on click
    $('#dropZone').on('click', function() {
        $('#fontUpload').click();
    });

    // Handle file selection via input
    $('#fontUpload').on('change', function() {
        var files = this.files;
        handleFiles(files);
    });

    // Function to handle files
    function handleFiles(files) {
        for (var i = 0; i < files.length; i++) {
            uploadFile(files[i]);
        }
    }

    function uploadFile(file) {
        var form_data = new FormData();
        form_data.append('file', file);

        // Validate file extension
        var ext = file.name.split('.').pop().toLowerCase();
        if(ext !== 'ttf') {
            alert('Please upload TTF files only.');
            return;
        }

        $.ajax({
            url: 'upload_font.php',
            type: 'POST',
            data: form_data,
            contentType: false,
            processData: false,
            success: function(response) {
                var res = JSON.parse(response);
                if(res.status) {
                    loadFonts();
                } else {
                    alert(res.message);
                }
            }
        });
    }

    // Add new row for selecting fonts
    $('#addRowButton').on('click', function() {
        var newRow = `
            <div class="fontGroupRow">
                <div class="form-group">
                    <input type="text" name="custom_font_names[]" class="form-control" placeholder="Custom Font Name" required />
                </div>
                <div class="form-group">
                    <select name="fonts[]" class="form-control" required>
                        <option value="" disabled selected>Select Font</option>
                        <!-- Options populated by JavaScript -->
                    </select>
                </div>
                <div class="form-group">
                    <img src="assets/cross.png" alt="Remove" class="removeRow" />
                </div>
            </div>
        `;
        $('#fontGroupFields').append(newRow);
        // Populate the new select field with font options without resetting others
        loadFonts();

        // Attach event handler to remove button
        $('.removeRow').off('click').on('click', function() {
            $(this).closest('.fontGroupRow').remove();
        });
    });

    // Attach a single submit handler to the form
    $('#fontGroupForm').on('submit', function(e) {
        e.preventDefault();
        if (isEditMode) {
            updateFontGroup();
        } else {
            createFontGroup();
        }
    });

    // Function to create a font group
    function createFontGroup() {
        var selectedFonts = $('select[name="fonts[]"]').map(function() {
            return $(this).val();
        }).get();

        if(selectedFonts.length < 2) {
            alert('Please select at least two fonts.');
            return;
        }

        var formData = $('#fontGroupForm').serialize();

        $.ajax({
            url: 'create_font_group.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                var res = JSON.parse(response);
                alert(res.message);
                if(res.status) {
                    loadFontGroups();
                    resetFontGroupForm();
                }
            }
        });
    }

    // Function to update a font group
    function updateFontGroup() {
        var selectedFonts = $('select[name="fonts[]"]').map(function() {
            return $(this).val();
        }).get();

        if(selectedFonts.length < 2) {
            alert('Please select at least two fonts.');
            return;
        }

        var formData = $('#fontGroupForm').serialize() + '&id=' + editGroupId;

        $.ajax({
            url: 'update_font_group.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                var res = JSON.parse(response);
                alert(res.message);
                if(res.status) {
                    loadFontGroups();
                    resetFontGroupForm();
                }
            }
        });
    }

    // Function to reset the form to create mode
    function resetFontGroupForm() {
        isEditMode = false;
        editGroupId = null;

        // Update form heading and button text
        $('#formHeading').text('Create Font Group');
        $('#formSubmitButton').text('Create');

        // Reset the form
        $('#fontGroupForm')[0].reset();
        $('#fontGroupFields').html(`
            <div class="fontGroupRow">
                <div class="form-group">
                    <input type="text" name="custom_font_names[]" class="form-control" placeholder="Custom Font Name" required />
                </div>
                <div class="form-group">
                    <select name="fonts[]" class="form-control" required>
                        <option value="" disabled selected>Select Font</option>
                        <!-- Options populated by JavaScript -->
                    </select>
                </div>
                <div class="form-group">
                    <img src="assets/cross.png" alt="Remove" class="removeRow" />
                </div>
            </div>
        `);
        loadFonts();

        // Attach event handler to remove button
        $('.removeRow').off('click').on('click', function() {
            $(this).closest('.fontGroupRow').remove();
        });
    }

    // Function to edit a font group
    function editFontGroup(groupId) {
        $.ajax({
            url: 'get_font_group.php',
            type: 'GET',
            data: { id: groupId },
            dataType: 'json',
            success: function(group) {
                isEditMode = true;
                editGroupId = groupId;

                // Update form heading and button text
                $('#formHeading').text('Edit Font Group');
                $('#formSubmitButton').text('Update');

                // Populate the form with group data
                $('#fontGroupForm')[0].reset();
                $('input[name="group_name"]').val(group.name);
                $('#fontGroupFields').empty();
                $.each(group.font_ids, function(index, font_id) {
                    var newRow = `
                        <div class="fontGroupRow">
                            <div class="form-group">
                                <input type="text" name="custom_font_names[]" class="form-control" placeholder="Custom Font Name" required />
                            </div>
                            <div class="form-group">
                                <select name="fonts[]" class="form-control" required>
                                    <option value="" disabled>Select Font</option>
                                    <!-- Options populated by JavaScript -->
                                </select>
                            </div>
                            <div class="form-group">
                                <img src="assets/cross.png" alt="Remove" class="removeRow" />
                            </div>
                        </div>
                    `;
                    $('#fontGroupFields').append(newRow);
                });

                // Set selected fonts and custom font names
                loadFonts(function() {
                    $('select[name="fonts[]"]').each(function(index) {
                        $(this).val(group.font_ids[index]);
                    });
                    $('input[name="custom_font_names[]"]').each(function(index) {
                        $(this).val(group.custom_font_names[index]);
                    });
                });

                // Attach event handler to remove button
                $('.removeRow').off('click').on('click', function() {
                    $(this).closest('.fontGroupRow').remove();
                });
            }
        });
    }

    // Delete font functionality
    $('#fontList').off('click', '.deleteFont').on('click', '.deleteFont', function() {
        var fontId = $(this).data('id');
        if (confirm('Are you sure you want to delete this font?')) {
            $.ajax({
                url: 'delete_font.php',
                type: 'POST',
                data: { id: fontId },
                success: function(response) {
                    var res = JSON.parse(response);
                    alert(res.message);
                    if (res.status) {
                        loadFonts();
                    }
                }
            });
        }
    });
});
</script>
</body>
</html>
