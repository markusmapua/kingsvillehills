<?php
require 'db_connect.php';


?>

<div class="modal fade" id="viewAnnouncementModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h5 id="viewTitle" class="font-weight-bold text-primary mb-1"></h5>
                <h6 id="viewDate" class="text-gray-600 mb-3 pb-2"></h6>
                <p id="viewMessage" class="text-gray-800" style="white-space: pre-wrap;"></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>