function editQuiz() {
    window.location.href = "../instructor/grammar-instructor-quizedit.php"; // Redirect to the edit quiz page
}

function viewResult() {
    window.location.href = "../instructor/grammar-instructor-result.php"; // Redirect to the view result page
}

// JavaScript functionality for the Grammar Quiz Edit page

document.getElementById('addQuestion').addEventListener('click', () => {
    const question = document.getElementById('question').value;
    const option1 = document.getElementById('option1').value;
    const option2 = document.getElementById('option2').value;
    const option3 = document.getElementById('option3').value;
    const option4 = document.getElementById('option4').value;
    const answer = document.getElementById('answer').value;

    if (!question || !option1 || !option2 || !option3 || !option4 || !answer) {
        alert('Please fill in all fields before adding the question.');
        return;
    }

    // Simulate saving the question (replace with actual save logic)
    console.log('Question added:', { question, option1, option2, option3, option4, answer });
    alert('Question added successfully!');

    // Clear the form after adding
    document.getElementById('quizForm').reset();
});

function goBack() {
    window.location.href = "../instructor/grammar-instructor.php"; // Redirect to grammar-instructor.php
}


