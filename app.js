document.getElementById("login").addEventListener("click", function(){
    document.querySelector(".popup").style.display = "flex";
});

document.getElementById("close").addEventListener("click", function(){
    document.querySelector(".popup").style.display = "none";
});

document.getElementById("close-succesfull-login").addEventListener("click", function(){
    document.querySelector(".popup-succesfull-login").style.display = "none";
});

