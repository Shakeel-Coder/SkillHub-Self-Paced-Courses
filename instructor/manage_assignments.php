<?php


    require_once '../auth/auth.php';

    include 'menu.php';

    // Restrict to logged-in users
    checkAccess();

    // Restrict to admins
    checkRole(['instructor']);

    

// Database connection
include '../config/config.php';

$instructorId = $_SESSION['id'];

// Fetch published assignments
$stmt = $conn->prepare("SELECT * FROM assignments WHERE status = 'Published' AND instructor_id = ?");
$stmt->execute([$instructorId]);
$publishedAssignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch draft assignments
$stmt = $conn->prepare("SELECT * FROM assignments WHERE status = 'Draft' AND instructor_id = ?");
$stmt->execute([$instructorId]);
$draftAssignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $assignmentId = $_POST['assignment_id'];
    if (isset($_POST['delete'])) {
        // Delete assignment
        $stmt = $conn->prepare("DELETE FROM assignments WHERE assignment_id = ?");
        $stmt->execute([$assignmentId]);
    } elseif (isset($_POST['publish'])) {
        // Publish assignment
        $stmt = $conn->prepare("UPDATE assignments SET status = 'Published' WHERE assignment_id = ?");
        $stmt->execute([$assignmentId]);
    } elseif (isset($_POST['unpublish'])) {
        // Unpublish assignment
        $stmt = $conn->prepare("UPDATE assignments SET status = 'Draft' WHERE assignment_id = ?");
        $stmt->execute([$assignmentId]);
    } elseif (isset($_POST['update'])) {
        // Redirect to update assignment page
        header("Location: update_assignment.php?assignment_id=$assignmentId");
        exit();
    } elseif (isset($_POST['submission'])) {
        // Redirect to assignment submissions page
        header("Location: assignment_submissions.php?assignment_id=$assignmentId");
        exit();
    }
    // Refresh the page to reflect changes
    header("Location: manage_assignments.php");
    exit();
}
?>


<div class="row">
    <div class="col-3">
        <?php include 'sidebar.php'; ?>
    </div>
    <div class="col-8">
        <div class="container mt-5">
            <h2>Manage Assignments</h2>
            <div class="mb-3">
                <a href="add_assignment.php" class="btn btn-success">Add New Assignment</a>
            </div>
            
            <h3>Published Assignments</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Sr.No.</th>
                        <th>Title</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Total Marks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($publishedAssignments)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No published assignments found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($publishedAssignments as $index => $assignment): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($assignment['assignment_title']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['closing_date']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['total_marks']); ?></td>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="assignment_id" value="<?php echo $assignment['assignment_id']; ?>">
                                        <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this assignment?');">Delete</button>
                                        <button type="submit" name="unpublish" class="btn btn-secondary btn-sm" onclick="return confirm('Are you sure you want to unpublish this assignment?');">Unpublish</button>
                                        <button type="submit" name="update" class="btn btn-primary btn-sm">Update</button>
                                        <button type="submit" name="submission" class="btn btn-info btn-sm">Submission</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <h3>Draft Assignments</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Sr.No.</th>
                        <th>Title</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Total Marks</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($draftAssignments)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No draft assignments found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($draftAssignments as $index => $assignment): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($assignment['assignment_title']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['closing_date']); ?></td>
                                <td><?php echo htmlspecialchars($assignment['total_marks']); ?></td>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="assignment_id" value="<?php echo $assignment['assignment_id']; ?>">
                                        <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this assignment?');">Delete</button>
                                        <button type="submit" name="publish" class="btn btn-primary btn-sm" onclick="return confirm('Are you sure you want to publish this assignment?');">Publish</button>
                                        <button type="submit" name="update" class="btn btn-primary btn-sm">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>