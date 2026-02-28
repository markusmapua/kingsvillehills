<?php
require 'db_connect.php';
?>

<div class="modal fade" id="createAnnouncementModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create Announcement</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            
            <form action="includes/post_announcement.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="announcementTitle">Title</label>
                        <input type="text" class="form-control" name="ann_title" required>
                    </div>
                    <div class="form-group">
                        <label for="announcementContent">Content</label>
                        <textarea class="form-control" name="ann_message" rows="4" required></textarea>
                    </div>
                </div> 
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-warning" type="submit" name="submit_announcement">Submit</button>
                </div>
            </form>

        </div>
    </div>
</div>