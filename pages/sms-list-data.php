<table>
    <thead>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Mobile</th>
    </thead>
    <tbody>
        <?php
            if(count($students) > 0){

                foreach($students as $student){

                    ?>
        <tr>
            <td><?php echo $student['id']; ?></td>
            <td><?php echo $student['name']; ?></td>
            <td><?php echo $student['email']; ?></td>
            <td><?php echo $student['phone_no']; ?></td>
        </tr>
        <?php
                }
            }else{

                echo "No data found";
            }
        ?>
    </tbody>
</table>