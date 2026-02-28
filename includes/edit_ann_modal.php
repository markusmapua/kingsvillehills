<?php
include 'db_connect.php';

?>

<div class="modal fade" id="editAnnouncementModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="exampleModalLabel">Edit Announcement</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            
            <form action="includes/update_announcement.php" method="POST">

                <input type="hidden" name="announcement_id" id="edit_id">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label for="announcementTitle">Title</label>
                        <input type="text" class="form-control" name="ann_title" id="edit_title" required>
                    </div>
                    <div class="form-group">
                        <label for="announcementContent">Content</label>
                        <textarea class="form-control" name="ann_message" id="edit_message" rows="4" required></textarea>
                    </div>
                </div> 
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-warning" type="submit" href ="includes/update_announcement.php">Edit Announcement</button>
                </div>
            </form>

        </div>
    </div>
</div>