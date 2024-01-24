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
                <?php 
                // echo "<pre>";
                // print_r($students);die;
                 if(count($students) > 0){

                    foreach($students as $student){

                        ?>
                <tr>
                    <td><?php echo $student['id']; ?></td>
                    <td><?php echo $student['name']; ?></td>
                    <td><?php echo $student['email']; ?></td>
                    <td>
                        <?php
                        echo '<img style="height:100px;width:100px" src="'.$student['profile_image'].'"/>';
                        ?>
                    </td>
                    <td><?php echo ucfirst($student['gender']); ?></td>
                    <td><?php echo $student['phone_no']; ?></td>
                    <td>
                        <a href="admin.php?page=student-system&action=edit&id=<?php echo $student['id']; ?>" class="btn-edit">Edit</a>
                        <a href="admin.php?page=student-system&action=view&id=<?php echo $student['id']; ?>" class="btn-view">View</a>
                        <a href="admin.php?page=student-system&action=delete&id=<?php echo $student['id']; ?>" onclick="return confirm('Are you sure want to delete?')" class="btn-delete">Delete</a>
                    </td>
                </tr>
                <?php
                    }
                 }
                ?>
            </tbody>

        </table>
    </div>

</div>