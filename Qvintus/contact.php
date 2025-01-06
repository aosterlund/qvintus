<?php
include_once 'includes/functions.php';    
include_once 'includes/header.php';
?>

<div class="container my-5">
    <h2 class="mb-4">Kontakta Oss</h2>
    <form>
        <div class="row mb-3">
            <div class="col">
                <label for="firstName" class="form-label">Förnamn</label>
                <input type="text" class="form-control" id="firstName" placeholder="Förnamn" required>
            </div>
            <div class="col">
                <label for="lastName" class="form-label">Efternamn</label>
                <input type="text" class="form-control" id="lastName" placeholder="Efternamn" required>
            </div>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Telefon</label>
            <input type="tel" class="form-control" id="phone" placeholder="Telefonnummer" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">E-post</label>
            <input type="email" class="form-control" id="email" placeholder="E-postadress" required>
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">Meddelande</label>
            <textarea class="form-control" id="message" rows="4" placeholder="Ditt meddelande" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Skicka</button>
    </form>
</div>

<?php
include_once 'includes/footer.php';
?>
