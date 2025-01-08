<?php include 'components/header.php'?>
<?php 
    if(isset($_GET['s']) && $_GET['s']!="")
    {
        $id =  preg_replace('/\D/', '', $_GET['s']);
        if($db->getIdByColumnValue("subject","id",$id,'id')=="")
        {
            echo '
            <script>
                window.location.href="dashboard.php"
            </script>
            ';
        }
    }else{
        echo '
        <script>
            window.location.href="dashboard.php"
        </script>
        ';
    }
     $subject_name = ucwords($db->getIdByColumnValue("subject","id",$id,"name"));
     $subject_code = $db->getIdByColumnValue("subject","id",$id,"code");

     $sectionRows = $db->getAllFacultySectionsBySubjectID($id);

          $semAndSchoolYearRows = $db->getSemAndSchoolyears($my_college_id);

     
         if(isset($_SESSION['dashboard']))
          {
            if(isset($_SESSION['temp_faculty_id']))
            {
                $faculty_id = $_SESSION['temp_faculty_id'];
                $title = 
              '<a href="facultymembers-info.php?i='.$faculty_id.'" class="mr-3">'. $_SESSION['dashboard'] .'</a>
              <p class="mr-3">></p>
              <a class="mr-3">' . htmlspecialchars($subject_name, ENT_QUOTES, 'UTF-8') . '</a>';
            }else{
                $title = 
              '<a href="facultymembers-info.php" class="mr-3">'. $_SESSION['dashboard'] .'</a>
              <p class="mr-3">></p>
              <a class="mr-3">' . htmlspecialchars($subject_name, ENT_QUOTES, 'UTF-8') . '</a>';
            }
          }else{
              // $title = '
              // <p class="mr-3">Documents</p> 
              // <p class="mr-3">></p>
              // <p class="mr-3">'.$subject_name.'</p>
  
              // ';
  
              $title = '
          <a href="departments.php" class="mr-3">Departments</a>
          <p class="mr-3">></p>
          <a href="courses.php?i='.$department_id.'" class="mr-3">' . htmlspecialchars($department_name, ENT_QUOTES, 'UTF-8') . '</a> 
          <a class="mr-3">></a>
          <a href="subjects.php?c='.$id.'" class="mr-3">11' . htmlspecialchars($course_name, ENT_QUOTES, 'UTF-8') . '</a>
      ';
$_SESSION['dashboard'] = $title;
    }
?>


    <style>
         .active-outline {
            outline: 1px solid #FFA500; /* Orange color */
            max-width: 100%;
            background-color: #FFFFFF; /* White */
            border-radius: 0.5rem; /* Rounded corners */
            border: 1px solid #E5E7EB; /* Border */
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06); /* Shadow */
        }
  
    </style>
        <div class="w-full h-screen overflow-x-hidden border-t flex flex-col">
            <main class="w-full flex-grow p-6">
                

                    <div class="container flex text-orange mb-3">
                        <?=$title?>
                   </div>

                <div class="container flex items-center mb-6">
                <h1 class="text-3xl text-black mr-3"><?=$subject_name?></h1>
                <select  id="schoolyearFilter" class="px-3 py-2 rounded-lg bg-orange text-orange">
                    <?php
                    if(count($semAndSchoolYearRows)>0)
                    {
                        foreach ($semAndSchoolYearRows as $row) {
                            $semester = $row['current_sem'];
                            $schoolyear = $row['current_year'];
                            $val = $semester.' - '.$schoolyear;
        
                            $selected = $row['current_status'] =='current' ? 'selected' : '';
        
                            echo '<option '.$selected.' value="'.$val.'">'.$val.'</option>';
                        } 
                    }else{
                        echo '<option  value="">Update Academic Year and Semester First </option>';
                    }
                    ?>
                </select>
                </div>

                <div  class="grid p-3  gap-x-20  md:grid-cols-2">

                    <div class="">
                    <div class="container flex mb-4 items-center">
                        <h1 class="text-base text-gray-500 font-bold mr-3">DOCUMENTS</h1>
                        <select id="documentFilter" class="px-3 py-2 text-base rounded-lg bg-orange text-orange">
                            <?php 
                             foreach ($sectionRows as $row) {
                                $section_name = $row['name'];
                                $section_id = $row['section_id'];
                                $faculty_id = $row['faculty_id'];
                                       $sy_section = $row['sy'];
                                $sem_section = $row['sem'];
                                $val = $sem_section.' - '.$sy_section;

                                echo '<option data-sy="'.$val.'" data-faculty_id="'.$faculty_id.'" value="'.$section_id.'">'.ucwords($section_name).'</option>';
                            }
                            ?>
                            
                        </select>
                    </div>

                    
                    <div id="documentDiv"></div>
                       
                    </div>
                <div>
                

                    <div class="overflow-y-scroll px-4" style="height:500px ">
                        <p class="text-gray-500 m- my-4 font-bold text-base">Faculty Members</p>
                        
                       <div id="facultyDiv"></div>
                    </div>
                </div>
            </div>
        </main>
        </div>
        
    </div>

<?php include 'modal/modal-documents-research.php' ?>
<?php include 'components/footer.php'?>
<script>
     $('.departments').addClass('active-nav-link');
     $('.departments').removeClass('opacity-75');
</script>
<script>
   function getDocument(subject_id, faculty_id, section_id, schoolyear) {
        $.ajax({
            url: 'controller/get_documents.php', // Endpoint URL
            type: 'POST', // HTTP request method
            data: {
                subject_id: subject_id,
                faculty_id: faculty_id,
                section_id: section_id,
                schoolyear: schoolyear
            }, // Send data to the server
            success: function(response) {
                $('#documentDiv').empty();
                $('#documentDiv').append(response); 
            },
            error: function(xhr, status, error) {
                console.error('Error:', status, error); // Log any errors
            }
        });
    }

    function getFaculty(subject_id, section_id, schoolyear) {
        $.ajax({
            url: 'controller/get_faculty.php', // Endpoint URL
            type: 'POST', // HTTP request method
            data: {
                subject_id: subject_id,
                section_id: section_id,
                schoolyear: schoolyear
            }, // Send data to the server
            success: function(response) {
                $('#facultyDiv').empty();
                $('#facultyDiv').append(response); 
            },
            error: function(xhr, status, error) {
                console.error('Error:', status, error); // Log any errors
            }
        });
    }
   

    function first_documentFilter(faculty_id,schoolyear)
    {
        var id = "";
        $('#documentFilter option').each(function(){
            if($(this).data('faculty_id') == faculty_id && $(this).data('sy')==schoolyear)
            {
                $(this).show();
                id = $(this).val();
                
            }else{
                $(this).hide();   
            }
        });
        return id;
    }
    function documentFilter(faculty_id,schoolyear)
    {
        $('#documentFilter option').each(function(){
            if($(this).data('faculty_id') == faculty_id && $(this).data('sy')==schoolyear)
            {
                $(this).show();
            }else{
                $(this).hide();   
            }
        });
    }

    function selectThefirst(first_section_id,first_faculty_id){
        var matchingOption = $('#documentFilter option').filter(function() {
            return $(this).val() === first_section_id && $(this).data('faculty_id') === first_faculty_id;
        });

        // Check if the option exists and then set it as the selected option
        if (matchingOption.length > 0) {
            matchingOption.prop('selected', true);
        } else {
            console.log('No matching option found with data-faculty_id = ' + first_faculty_id + ' and value = ' + first_section_id);
        }
    }
</script>

<script>
$(document).ready(function() {
    // Select the first card and get the faculty_id
  
    var first_schoolyear = $('#schoolyearFilter').val();
    const subject_id = <?php echo json_encode($id)?>;

    var section_id = $('#documentFilter').val()


    $.ajax({
            url: 'controller/get_faculty.php', // Endpoint URL
            type: 'POST', // HTTP request method
            data: {
                subject_id: subject_id,
                section_id: section_id,
                schoolyear: first_schoolyear
            }, // Send data to the server
            success: function(response) {
                $('#facultyDiv').empty();
                $('#facultyDiv').append(response); 


                var $firstCard = $('.card').first();
                var first_faculty_id = $firstCard.data('faculty-id');
                $firstCard.addClass('active-outline');

                var first_section_id = (first_documentFilter(first_faculty_id,first_schoolyear))
                selectThefirst(first_section_id,first_faculty_id)
                getDocument(subject_id,first_faculty_id,first_section_id,first_schoolyear);





                $('.card').click(function(){
                    $('.card').removeClass('active-outline');
                    $(this).addClass('active-outline');
                    var faculty_id = $(this).data('faculty-id');
                    // var section_id = $('#documentFilter').val();
                     var schoolyear = $('#schoolyearFilter').val();
                    documentFilter(faculty_id,schoolyear );
                    var section_id = (first_documentFilter(faculty_id,schoolyear))
                    selectThefirst(section_id,faculty_id)
                    getDocument(subject_id,faculty_id,section_id,schoolyear);
                })

                $('#documentFilter').change(function(){
                    var section_id = $(this).val();
                    var schoolyear = $('#schoolyearFilter').val();
                    var $activeCard = $('.card.active-outline');
                    // Optional: Do something with the active card
                    if ($activeCard.length > 0) {
                        var faculty_id = $activeCard.data('faculty-id');
                        getDocument(subject_id,faculty_id,section_id,schoolyear);
                    } 
                })
                $('#schoolyearFilter').change(function(){
                    var section_id = $('#documentFilter').val();
                    var schoolyear = $(this).val();


                    $.ajax({
                        url: 'controller/get_faculty.php', // Endpoint URL
                        type: 'POST', // HTTP request method
                        data: {
                            subject_id: subject_id,
                            section_id: section_id,
                            schoolyear: schoolyear
                        }, // Send data to the server
                        success: function(response) {
                            $('#facultyDiv').empty();
                            $('#facultyDiv').append(response); 

                            var $activeCard = $('.card.active-outline');
                            if ($activeCard.length > 0) {
                                var faculty_id = $activeCard.data('faculty-id');
                                getDocument(subject_id,faculty_id,section_id,schoolyear);
                            
                            } 


                            $('.card').click(function(){
                                $('.card').removeClass('active-outline');
                                $(this).addClass('active-outline');
                                var faculty_id = $(this).data('faculty-id');
                                // var section_id = $('#documentFilter').val();
                                var schoolyear = $('#schoolyearFilter').val();
                                documentFilter(faculty_id,schoolyear);
                                var section_id = (first_documentFilter(faculty_id,schoolyear))
                                selectThefirst(section_id,faculty_id)
                                getDocument(subject_id,faculty_id,section_id,schoolyear);
                            })



                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', status, error); // Log any errors
                        }
                    });
                    
                 
                })

            },
            error: function(xhr, status, error) {
                console.error('Error:', status, error); // Log any errors
            }
        });

 


    


});

</script>

