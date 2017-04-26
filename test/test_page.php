<form method="post" action="receiver.php">
    <input type="hidden" name="URL" value="https://en.wikipedia.org/wiki/Juan_Manuel_Santos">
    <input type="submit">
</form>

<form action="receiver.php" method="post" enctype="multipart/form-data">
    Select FILE to upload:
    <input type="file" name="DOCX" id="fileToUpload">
    <input type="submit" value="Upload Image" name="submit">
</form>