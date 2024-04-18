<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST["name"];
    $roll = $_POST["roll"];
    $section = $_POST["section"];

    // Path to your zip file containing the .docx template
    $zipFilePath = "https://github.com/BarnawarSumit/testing-okay/raw/main/template.zip";

    // Extract the zip file
    $zip = new ZipArchive;
    if ($zip->open($zipFilePath) === TRUE) {
        // Extract the .docx file
        $zip->extractTo("/tmp"); // Extract to a temporary directory
        $zip->close();

        // Load the extracted .docx file
        $docxFilePath = "/tmp/template.docx";
        $doc = new ZipArchive;
        if ($doc->open($docxFilePath) === TRUE) {
            // Replace placeholders with form data
            $content = $doc->getFromName("word/document.xml");
            $content = str_replace("{NAME}", $name, $content);
            $content = str_replace("{ROLL_NUMBER}", $roll, $content);
            $content = str_replace("{CLASS_SECTION}", $section, $content);
            $doc->deleteName("word/document.xml");
            $doc->addFromString("word/document.xml", $content);
            $doc->close();

            // Send the modified .docx file to the client
            header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
            header("Content-Disposition: attachment; filename=modified_project.docx");
            readfile($docxFilePath);
        } else {
            echo "Failed to open the .docx file";
        }
    } else {
        echo "Failed to open the zip file";
    }
    // Clean up temporary files if needed
    unlink($docxFilePath);
    exit;
}
?>
