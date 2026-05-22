<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resume | Carabbacan </title>

    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <?php
        $name = "Tristan Sam R. Carabbacan";
        $email = "tristancarabbcan06@gmail.com";
        $phone = "+63 917 555 5192";
        $address = "Quezon City";

        $college = "Far Eastern University - Institute of Technology";
        $college_degree = "Bachelor of Science in Information Technology";
        $college_graduate = "June, 2028";

        $senior_highschool = "Top Achiever Private School";
        $senior_highschool_degree = "Senior High School - Science, Technology, Engineering, and Mathematics (STEM) Strand";
        $senior_highschool_graduate = "June, 2024";

        $work_experience_1 = "Parish Pastoral Council for Responsible Voting Through FEU's NSTP Program";
        $work_experience_1_position = "Volunteer";
        $work_experience_1_duration = "May 2025";

        $work_experience_2 = "National Irrigation Administration";
        $work_experience_2_position = "Information Technology Intern (80 Hours)";
        $work_experience_2_duration = "March-April 2025";
        
        $certification_1 = "Certiport Information Technology Specialist - Java";
        $certification_2 = "Certiport Information Technology Specialist - Databases";
        $certification_3 = "Certiport Information Technology Specialist - HTML and CSS";
        $certification_4 = "Certiport Information Technology Specialist - Javascript";
        $certification_5 = "Certiport Information Technology Specialist - Python";

        $skill_1 = "C++";
        $skill_2 = "HTML and CSS";
        $skill_3 = "JavaScript";
        $skill_4 = "Python";
        $skill_5 = "Microsoft Suite";
        $skill_6 = "Google Suite";
    ?>

    <main class="resume">
        <header class="resume-header">
            <div class="headline">
                <h1><?= $name ?></h1>
            </div>
        </header>

        <div class="resume-body">
            <section class="left-column">
                <div class="section about">
                    <div class="section-heading">
                        <span>Education</span>
                    </div>
                    <div class="education_1">
                        <h2><?= $college ?></h2>
                        <p><?= $college_degree ?></p>
                    
                        <ul>
                            <li>FEU Tech CCSMA Dean's Lister - Bronze & Silver (S.Y. 2024-2026)</li>
                            <li>Relevant Coursework: Computer Programming, Data Structures and Algorithm, Web Design</li>
                        </ul>
                    
                        <p>Expected Date of Graduation: <?= $college_graduate ?></p>
                    </div>
                    <div class="education_2">
                        <h2><?= $senior_highschool ?></h2>
                        <p><?= $senior_highschool_degree ?></p>
                        
                        <ul>
                            <li>With Honors</li>
                        </ul>
                        
                        <p>Graduated: <?= $senior_highschool_graduate ?></p>
                    </div>
                </div>

                <div class="section work-experience">
                    <div class="section-heading">
                        <span>Work Experience</span>
                    </div>

                    <div class="work_experience_1">
                        <h2><?= $work_experience_1 ?></h2>
                        <p><?= $work_experience_1_position ?></p>
                    
                        <ul>
                            <li>Assisted in verifying the accuracy of electoral votes by cross-checking system results with the provided precint-level data.</li>
                            <li>Collaborated with team memebers to efficiently complete the audit of electoral votes during the recent presidential election.</li>
                        </ul>
                    
                        <p><?= "Duration: " . $work_experience_1_duration ?></p>
                    </div>
                    
                    <div class="work_experience_2">
                        <h2><?= $work_experience_2 ?></h2>
                        <p><?= $work_experience_2_position ?></p>
                    
                        <ul>
                            <li>Assisted an IT staff in troubleshooting and resolving hardware issues, including repairing malfunctioning office printers.</li>
                            <li>Edited and refined a 3D model of the Magat Dam for use in a public parade display.</li>
                            <li>Collaborated with a fellow intern to edit and produce a video presentation for the inauguration ceremony of solar pumps in Ballesteros, Auroa, Isabela.</li>
                        </ul>
                    
                        <p><?= "Duration: " . $work_experience_2_duration ?></p>
                    </div>
                </div>
            </section>

            <aside class="right-column">
                <div class="contacts">
                    <div class="section-heading">
                        <span>Contacts</span>
                    </div>
                    <ul>
                        <li>Email: <?= $email ?></li>
                        <li>Phone: <?= $phone ?></li>
                        <li>Address: <?= $address ?></li>
                    </ul>   
                </div>

                <div class="certification">
                    <div class="section-heading">
                        <span>Certification</span>
                    </div>

                    <ul>
                        <li><?= $certification_1 ?></li>
                        <li><?= $certification_2 ?></li>
                        <li><?= $certification_3 ?></li>
                        <li><?= $certification_4 ?></li>
                        <li><?= $certification_5 ?></li>
                    </ul>   
                </div>

                <div class="skills">
                    <div class="section-heading">
                        <span>Skills</span>
                    </div>
                    <ul class="skills-list">
                        <li><?= $skill_1 ?></li>
                        <li><?= $skill_2 ?></li>
                        <li><?= $skill_3 ?></li>
                        <li><?= $skill_4 ?></li>
                        <li><?= $skill_5 ?></li>
                        <li><?= $skill_6 ?></li>
                    </ul>
                </div>
            </aside>
        </div>
    </main>
</body>
</html>
