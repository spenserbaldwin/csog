<?php
    if (!isset($questionsClass))
        $questionsClass = new Questions($db);
    
    $sectionsTable = new DbObject($db, "sections", false);
    $categoriesTable = new DbObject($db, "question_categories", false);
    
    $action = getParameterString("action");
    $sectionID = getParameterNumber("sectionID");
    $questionID = getParameterNumber("questionID");
    $categoryID = getParameterNumber("categoryID");

    
    if ($sectionID <= 0 && $questionID <= 0)
    {
        out("Invalid parameters");
        die();
    }
    
    if (!empty($action))
    {
        if ($action == "save-category")
        {
            $categoriesTable->data['title'] = $_POST['title'];
            $categoriesTable->data['sectionID'] = (int)$_POST['sectionID'];
            $categoriesTable->data['type'] = $_POST['type'];
            $categoriesTable->data['spawn_box_label'] = $_POST['spawn_box_label'];
            $categoriesTable->data['`order`'] = (int)$_POST['order'];
            
            if ($categoryID > 0)
            {
                $categoriesTable->data['id'] = $categoryID;
                $id = $categoriesTable->update();
            }
            else
                $id = $categoriesTable->create();
        }
        
        if ($action == "delete-category")
        {
            if ($categoryID > 0)
            {
                $categoriesTable->data['id'] = $categoryID;
                $categoriesTable->delete();
            }
        }
        
        if ($action == 'save')
        {
            $title = getParameterString("title");
            $hint = getParameterString("hint");
            $categoryID = getParameterNumber("categoryID");
            $type = getParameterString("type");
            $order = getParameterNumber("order");
            
            $questionsClass->saveQuestion($title, $hint, $categoryID, $type, $order, $questionID);
        }
        if ($action == "delete")
        {
            $questionsClass->deleteQuestion($questionID);
        }
        
        $action = "display-all";
    }
    else
    {
        if ($questionID > 0)
            $action = "display-one";
        else
            $action = "display-all";
    }
    
    if ($action == "display-all")
    {
        $categories = $categoriesTable->fetchAll(" WHERE `sectionID` = $sectionID ORDER BY `order`");
        
        echo "
            <div class='submenu'>
                <a href='/admin/sections/edit'>Back to sections</a>
                <a style='margin-left:20px;' href='/admin/questions/add?sectionID=$sectionID'>Add new question</a>
                <a style='margin-left:20px;' href='/admin/categories/add?sectionID=$sectionID'>Add new category</a>
            </div>
            <br />
        ";

        echo "
            <table>
                <thead>
                    <tr>
                        <th scope='col'>Title</th>
                        <th scope='col'>Type</th>
                        <th scope='col'>Order</th>
                        <th scope='col'>Actions</th>
                    </tr>
                </thead>
                <tbody>
        ";
        
        foreach ($categories as $category)
        {
            echo "
                <tr>
                    <td><span style='font-weight:800;'>{$category['title']}</span></td>
                    <td><span style='font-weight:800;'>{$category['type']}</span></td>
                    <td><span style='font-weight:800;'>{$category['order']}</span></td>
                    <td>
                        <span style='font-weight:800;'>
                            <a href='/admin/categories/edit?sectionID={$sectionID}&amp;categoryID={$category['id']}'>Edit</a>
                            <a href='/admin/questions/edit?action=delete-category&amp;sectionID={$sectionID}&amp;categoryID={$category['id']}'>Delete</a>
                        </span>
                    </td>
                </tr>
            ";
                        
            printQuestions($category['id']);
        }
        
        

        echo "</tbody></table>";
    }
    elseif ($action == "display-one")
    {
        require_once ("singleQuestion.php");
    }
    
    
    function printQuestions($categoryID)
    {
        global $questionsClass, $sectionID;
        
        $questions = $questionsClass->listQuestions($categoryID);
        
        foreach ($questions as $question)
        {

            echo "
                <tr>
                    <td style='padding-left:10px'>{$question['title']}</td>
                    <td style='padding-left:10px'>{$question['type']}</td>
                    <td style='padding-left:10px'>{$question['order']}</td>
                    <td>
                        <a href='/admin/questions/edit?sectionID={$sectionID}&amp;questionID={$question['id']}'>Edit</a>
                        <a href='/admin/questions/edit?action=delete&amp;sectionID={$sectionID}&amp;questionID={$question['id']}'>Delete</a>
                    </td>
                </tr>
            ";
        }
    }
?>