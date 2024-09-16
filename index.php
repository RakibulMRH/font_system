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
        <img src="public/upload_icon.png" id="uploadIcon" width="10%" height="10%"/>
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
                    <img src="public/cross.png" alt="Remove" class="removeRow" />
                </div>
            </div>
        </div>
        <div class="form-actions mt-3">
            <button type="button" id="addRowButton" class="btn btn-add-row">+Add Row</button>
            <div>
                <button type="submit" id="formSubmitButton" class="btn btn-create">Create</button>
                <button type="button" id="cancelEditButton" class="btn btn-danger" style="display: none;">Cancel</button>
            </div>
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
<script src='js/main.js'></script>
</body>
</html>
