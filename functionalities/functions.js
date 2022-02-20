//const content = document.getElementById("main-content");

function registerToConference() {
    content.innerHTML = `
    <div class="container-fluid text-center">
        <h2>Registrati</h2>
        <hr class="my-4">
        <form action="registerToConference.php" method="post" class="container my-5">
            <div class="mb-3 form-group floating">
                <input type="text" class="form-control floating" name="username" required autocomplete="off">
                <label for="username">Username</label>          
            </div>
            <div class="mb-3 form-group floating">
                <input type="text" class="form-control floating" name="acronimo" required autocomplete="off">
                <label for="acronimo">Acronimo Conferenza</label> 
            </div>
            <div class="mb-3 form-group floating">
                <input type="number" class="form-control floating" name="annoEdizione" required autocomplete="off">
                <label for="annoEdizione">Anno Edizione</label>          
            </div>
            <div class="container text-center my-5">
                <button type="submit" class="btn btn-primary">Register</button>
            </div>
        </form>
    </div>
    `;
}
