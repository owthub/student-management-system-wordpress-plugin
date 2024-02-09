<div class="sms-plugin table-card">

    <h2>List Student</h2>

    <?php if(!empty($displayMessage)){
        ?>  
        <div class="display-success">
           <?php echo $displayMessage; ?>
        </div>
        <?php
    } ?>

    <div class="table-container">

        <table class="student-table" id="tbl-student-table">

            <thead>
                <th>#ID</th>
                <th>#Name</th>
                <th>#Email</th>
                <th>#Profile Image</th>
                <th>#Gender</th>
                <th>#Phone</th>
                <th>Action</th>
            </thead>

            <tbody>
                
            </tbody>

        </table>
    </div>

</div>