const searchOrderedBy_Selector = document.getElementById("sortSelect");


document.addEventListener("DOMContentLoaded", function () {
    searchOrderedBy_Selector.addEventListener("change", function () {
        updateQuestions();
    });
});
function updateQuestions(){
    const error = document.getElementById("error");
        // Perform an AJAX request to your Laravel backend
        fetch(`/api/myquestions/${user_id.textContent}?OrderBy=${searchOrderedBy_Selector.value}`)
            .then(response => {
                if (response.status == 200) {
                    return response.json();
                }else{
                    error.textContent = "Error fetching questions";
                }
            })
            .then(data => {
                // Update the search results in the DOM
                showPage(data.data,data.links);
            })
            .catch(error => {
                console.error('Error fetching search results', error);
            });

}
window.onload = function () {
    updateQuestions();
}   

function showPage(results,links){
    const Questions = document.getElementById("Questions");
    Questions.innerHTML = "";
    if(results.length == 0){
        Questions.innerHTML = "No questions Found";
    }
    for (let i = 0; i < results.length; i++) {
        let result = results[i];
        // Create the main answer card div
        const questionCard = document.createElement("li");
        questionCard.classList.add("question");

        //votes
        const votes = document.createElement("div");
        votes.classList.add("votes");

        const answernum = document.createElement("p");
        answernum.classList.add("answernum");
        answernum.textContent = result.answernum + " answers"; // Replace with actual data
        // Create the <p> element with the class "votesnum" and set its content dynamically using data from the server
        const votesNum = document.createElement("p");
        votesNum.classList.add("votesnum");
        votesNum.textContent = result.votes + " votes"; // Replace with actual data





        votes.appendChild(answernum);
        votes.appendChild(votesNum);


        // Content
        const contentDiv = document.createElement("div");
        contentDiv.classList.add("content");

        const questionLink = document.createElement("a");
        questionLink.href = `/question/${result.id}`; // Replace with actual URL

        const questionTitle = document.createElement("h3");
        questionTitle.textContent = result.title; // Replace with actual title

        questionLink.appendChild(questionTitle);

        const profileInfoDiv = document.createElement("div");
        profileInfoDiv.classList.add("profileinfo");

        const userProfileLink = document.createElement("a");
        userProfileLink.href = `/profile/${result.userid}`; // Replace with actual URL
        userProfileLink.textContent = result.username; // Replace with actual username

        const questionDate = document.createElement("p");
        questionDate.textContent = result.date; // Replace with actual date

        const questionbottom= document.createElement("div");
        questionbottom.classList.add("questionbottom");

        const questiontags = document.createElement("div");
        questiontags.classList.add("tags");

       // Split the comma-separated strings into arrays
       const tagsArray = result.tags ? result.tags.split(',') : [result.tags];
       const tagsidArray = result.tagsid ? result.tagsid.split(',') : [result.tagsid];

        // Create a dictionary object with tag IDs as keys and tag names as values
        for (let j = 0; j < tagsArray.length; j++) {
            if(tagsArray[j] == null) continue;
            const tagElement = document.createElement("div");
            tagElement.classList.add("tag");
        
            const tagLink = document.createElement("a");
            tagLink.href = `/tag/${tagsidArray[j]}`;
            tagLink.textContent = tagsArray[j];
        
            tagElement.appendChild(tagLink);
            questiontags.appendChild(tagElement);
        }

        var correctdiv = document.createElement("div");

        // Set the class attribute
        correctdiv.className = "correct";

        // Set the style attribute
        correctdiv.style.display = "flex";
        correctdiv.style.color = "green";
        correctdiv.style.alignItems = "center";


        // Create a new span element
        var newSpan = document.createElement("span");

        // Set the class attribute for the span element
        newSpan.className = "material-symbols-outlined";

        // Set the inner text of the span element
        newSpan.innerText = "check";

        // Append the span element to the div element
        correctdiv.appendChild(newSpan);


        contentDiv.appendChild(questionLink);

        profileInfoDiv.appendChild(userProfileLink);
        profileInfoDiv.appendChild(questionDate);

        questionbottom.appendChild(questiontags);
        questionbottom.append(profileInfoDiv);

        contentDiv.appendChild(questionbottom);
        if(result.correctanswerid != null){
            questionCard.appendChild(correctdiv);
        }
        questionCard.appendChild(votes);
        questionCard.appendChild(contentDiv);

        // Append the answer card to the search results
        Questions.appendChild(questionCard);
        
    }
    renderPaginationButtons(links);
}

