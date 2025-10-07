<?php
header('Content-Type: application/json');
session_start();

// Read raw POST data and decode JSON
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

$message = strtolower(trim($input['message'] ?? ''));

// Default reply
$reply = "Sorry, I didn't understand that. Could you ask differently?";

// If message empty
if ($message === '') {
    $reply = "Please say something to start the chat.";
} else {
    // Define patterns and responses
    $patterns = [
        '/\b(hello|hi|hey|good morning|good afternoon|good evening)\b/' => function() {
            $_SESSION['last_topic'] = null;
            return "Hi there! How can I help you today?";
        },
        '/\b(admission|enroll|registration|apply|open)\b/' => function() {
            $_SESSION['last_topic'] = 'admission';
            return "Admissions are open! Please check the Portal for registration.";
        },
        '/\b(location|where are you|address|we de|we de kam|we de ya)\b/' => function() {
            $_SESSION['last_topic'] = 'location';
            return "We are located in Kaffu Bullom Chiefdom, Lungi.";
        },
        '/\b(courses|subjects|classes|programs)\b/' => function() {
            $_SESSION['last_topic'] = 'courses';
            return "We offer courses in Science, Mathematics, ICT, Social Studies, and more.";
        },
        '/\b(contact|phone|email|call)\b/' => function() {
            $_SESSION['last_topic'] = 'contact';
            return "You can reach us through the Contact page or email us at royal@academy.edu";
        },
        // Context-aware follow-up questions example:
        '/\b(fees|cost|price|tuition)\b/' => function() {
            if (isset($_SESSION['last_topic']) && $_SESSION['last_topic'] === 'courses') {
                return "Course fees vary depending on the program. Please check the fees page on our website.";
            }
            return "Could you please specify which fees you're asking about?";
        },
        '/\b(holiday|break|vacation)\b/' => function() {
            return "Our next school holiday starts on August 15th and lasts two weeks.";
        },
        '/\b(school time|hours|opening hours|time)\b/' => function() {
            return "Our school hours are from 8:00 AM to 3:00 PM, Monday to Friday.";
        },
        '/\b(transport|bus|shuttle)\b/' => function() {
            return "We provide a school shuttle service for students living within 10km.";
        },
        '/\b(exam|tests|assessment)\b/' => function() {
            return "Exams are held at the end of each term. Check the academic calendar for details.";
        },
        '/\b(scholarship|financial aid|support)\b/' => function() {
            return "We offer scholarships based on merit and need. Visit the scholarships page for info.";
        },
        '/\b(teacher|staff|faculty)\b/' => function() {
            return "Our teachers are highly qualified and dedicated to student success.";
        },
        '/\b(library|resources|books)\b/' => function() {
            return "The school library is open from 8 AM to 4 PM on school days.";
        },
        '/\b(lunch|cafeteria|food)\b/' => function() {
            return "Lunch is provided daily in the cafeteria with healthy meal options.";
        },
        '/\b(transport|bus|shuttle)\b/' => function() {
            return "We provide shuttle services for students. Routes are available on our website.";
        },
        '/\b(sports|games|activities)\b/' => function() {
            return "We offer a variety of sports and extracurricular activities after school.";
        },
        '/\b(admission requirements|criteria)\b/' => function() {
            return "Admission requires previous school records and an entrance exam.";
        },
        '/\b(graduation|certificate|diploma)\b/' => function() {
            return "We award certificates and diplomas upon successful completion of courses.";
        },
        '/\b(help|support|problem|issue)\b/' => function() {
            return "How can I assist you? Please provide details of the issue.";
        },
    ];

    // Match patterns
    foreach ($patterns as $pattern => $responseFunc) {
        if (preg_match($pattern, $message)) {
            $reply = $responseFunc();
            break;
        }
    }
}

echo json_encode(['reply' => $reply]);
