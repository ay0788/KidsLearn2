document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registration-form');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form values
        const parentName = document.getElementById('parent-name').value;
        const childName = document.getElementById('child-name').value;
        const childAge = document.getElementById('child-age').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        // Basic validation
        if (!parentName || !childName || !childAge || !email || !password) {
            showError('Veuillez remplir tous les champs');
            return;
        }
        
        if (childAge < 3 || childAge > 10) {
            showError('L\'âge de l\'enfant doit être compris entre 3 et 10 ans');
            return;
        }
        
        if (!isValidEmail(email)) {
            showError('Veuillez entrer une adresse email valide');
            return;
        }
        
        if (password.length < 8) {
            showError('Le mot de passe doit contenir au moins 8 caractères');
            return;
        }
        
        // Send data to server
        fetch('api/signup.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                parentName,
                childName,
                childAge,
                email,
                password
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message);
                // Redirect to parents page after 2 seconds
                setTimeout(() => {
                    window.location.href = 'parents.html';
                }, 2000);
            } else {
                showError(data.message);
            }
        })
        .catch(error => {
            showError('Une erreur est survenue lors de l\'inscription');
            console.error('Error:', error);
        });
    });
    
    function showError(message) {
        // Create error message element if it doesn't exist
        let errorElement = document.querySelector('.error-message');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            form.insertBefore(errorElement, form.firstChild);
        }
        
        errorElement.textContent = message;
        errorElement.style.display = 'block';
        
        // Hide error after 3 seconds
        setTimeout(() => {
            errorElement.style.display = 'none';
        }, 3000);
    }
    
    function showSuccess(message) {
        // Create success message element if it doesn't exist
        let successElement = document.querySelector('.success-message');
        if (!successElement) {
            successElement = document.createElement('div');
            successElement.className = 'success-message';
            form.insertBefore(successElement, form.firstChild);
        }
        
        successElement.textContent = message;
        successElement.style.display = 'block';
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
});