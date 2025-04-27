// Fonctions pour les modales
function showModal(modalId) {
    console.log(`Tentative d'ouverture de la modale: ${modalId}`);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'block';
        console.log(`Modale ${modalId} ouverte avec succès`);
    } else {
        console.error(`Modale ${modalId} non trouvée`);
    }
}

function closeModal(modalId) {
    console.log(`Tentative de fermeture de la modale: ${modalId}`);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        console.log(`Modale ${modalId} fermée avec succès`);
    } else {
        console.error(`Modale ${modalId} non trouvée`);
    }
}

// Gestion des catégories
function showAddCategoryModal() {
    console.log('Tentative d\'ouverture de la modale catégorie');
    const form = document.getElementById('category-form');
    if (form) {
        form.reset();
    }
    showModal('category-modal');
}

async function loadCategories() {
    try {
        const response = await fetch('../api/admin/categories.php', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('adminToken')}`
            }
        });
        const data = await response.json();
        
        if (data.success) {
            // Mettre à jour la liste des catégories
            const categoriesList = document.getElementById('categories-list');
            categoriesList.innerHTML = data.categories.map(category => `
                <tr>
                    <td>${category.name}</td>
                    <td>${category.description || ''}</td>
                    <td><i class="${category.icon}"></i></td>
                    <td>
                        <button class="action-button edit-button" onclick="editCategory(${category.id})">
                            <i class="fas fa-edit"></i> Modifier
                        </button>
                        <button class="action-button delete-button" onclick="deleteCategory(${category.id})">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </td>
                </tr>
            `).join('');

            // Mettre à jour le select des catégories dans le formulaire d'élément
            const categorySelect = document.getElementById('element-category');
            if (categorySelect) {
                categorySelect.innerHTML = data.categories.map(category => `
                    <option value="${category.id}">${category.name}</option>
                `).join('');
            }
        }
    } catch (error) {
        console.error('Erreur lors du chargement des catégories:', error);
    }
}

// Gestion des éléments
function showAddElementModal() {
    console.log('Tentative d\'ouverture de la modale élément');
    const form = document.getElementById('element-form');
    if (form) {
        form.reset();
        loadCategoriesForSelect();
    }
    showModal('element-modal');
}

async function loadElements() {
    try {
        const response = await fetch('../api/admin/elements.php', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('adminToken')}`
            }
        });
        const data = await response.json();
        
        if (data.success) {
            const elementsList = document.getElementById('elements-list');
            elementsList.innerHTML = data.elements.map(element => `
                <tr>
                    <td>${element.name}</td>
                    <td>${element.category_name}</td>
                    <td>${element.type}</td>
                    <td>
                        <button class="action-button edit-button" onclick="editElement(${element.id})">
                            <i class="fas fa-edit"></i> Modifier
                        </button>
                        <button class="action-button delete-button" onclick="deleteElement(${element.id})">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </td>
                </tr>
            `).join('');
        }
    } catch (error) {
        console.error('Erreur lors du chargement des éléments:', error);
    }
}

// Gestion des médias
function showAddMediaModal() {
    console.log('Tentative d\'ouverture de la modale média');
    const form = document.getElementById('media-form');
    if (form) {
        form.reset();
    }
    showModal('media-modal');
}

async function loadMedia() {
    try {
        const response = await fetch('../api/admin/media.php', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('adminToken')}`
            }
        });
        const data = await response.json();
        
        if (data.success) {
            const mediaList = document.getElementById('media-list');
            mediaList.innerHTML = data.media.map(media => `
                <div class="media-item">
                    ${media.type === 'image' ? 
                        `<img src="${media.file_path}" class="media-preview" alt="${media.title}">` :
                        `<div class="media-preview">
                            <i class="fas fa-${media.type === 'audio' ? 'music' : 'video'} fa-3x"></i>
                        </div>`
                    }
                    <div class="media-info">
                        <h4 class="media-title">${media.title}</h4>
                        <p>${media.description || ''}</p>
                        <div class="media-actions">
                            <button class="action-button edit-button" onclick="editMedia(${media.id})">
                                <i class="fas fa-edit"></i> Modifier
                            </button>
                            <button class="action-button delete-button" onclick="deleteMedia(${media.id})">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }
    } catch (error) {
        console.error('Erreur lors du chargement des médias:', error);
    }
}

// Chargement des catégories pour le select des éléments
async function loadCategoriesForSelect() {
    try {
        const response = await fetch('../api/admin/categories.php', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('adminToken')}`
            }
        });
        const data = await response.json();
        
        if (data.success) {
            const select = document.getElementById('element-category');
            select.innerHTML = data.categories.map(category => 
                `<option value="${category.id}">${category.name}</option>`
            ).join('');
        }
    } catch (error) {
        console.error('Erreur lors du chargement des catégories:', error);
        alert('Erreur lors du chargement des catégories');
    }
}

// Gestion des champs de contenu
function showContentFields() {
    const type = document.getElementById('element-type').value;
    
    // Cacher tous les champs de contenu
    document.querySelectorAll('.content-fields').forEach(field => {
        field.style.display = 'none';
    });

    // Afficher les champs appropriés selon le type
    switch(type) {
        case 'text':
            document.getElementById('text-fields').style.display = 'block';
            break;
        case 'table':
            document.getElementById('table-fields').style.display = 'block';
            break;
        case 'list':
            document.getElementById('list-fields').style.display = 'block';
            break;
        case 'image':
        case 'audio':
        case 'video':
            document.getElementById('media-fields').style.display = 'block';
            break;
        case 'quiz':
            document.getElementById('quiz-fields').style.display = 'block';
            break;
        case 'matching':
            document.getElementById('matching-fields').style.display = 'block';
            break;
    }
}

// Génération de l'éditeur de tableau
function generateTableEditor() {
    const rows = parseInt(document.getElementById('table-rows').value);
    const cols = parseInt(document.getElementById('table-cols').value);
    const editor = document.getElementById('table-editor');

    let tableHTML = '<table>';
    for (let i = 0; i < rows; i++) {
        tableHTML += '<tr>';
        for (let j = 0; j < cols; j++) {
            tableHTML += `<td><input type="text" placeholder="Cellule ${i+1},${j+1}"></td>`;
        }
        tableHTML += '</tr>';
    }
    tableHTML += '</table>';

    editor.innerHTML = tableHTML;
}

// Ajout d'option de quiz
function addQuizOption() {
    const options = document.getElementById('quiz-options');
    const newOption = document.createElement('div');
    newOption.className = 'quiz-option';
    newOption.innerHTML = `
        <input type="text" placeholder="Nouvelle option">
        <input type="checkbox"> Correcte
        <button type="button" class="action-button delete-button" onclick="this.parentElement.remove()">
            <i class="fas fa-trash"></i>
        </button>
    `;
    options.appendChild(newOption);
}

// Ajout de paire d'association
function addMatchingPair() {
    const pairs = document.getElementById('matching-pairs');
    const newPair = document.createElement('div');
    newPair.className = 'matching-pair';
    newPair.innerHTML = `
        <input type="text" placeholder="Élément gauche">
        <span>→</span>
        <input type="text" placeholder="Élément droite">
        <button type="button" class="action-button delete-button" onclick="this.parentElement.remove()">
            <i class="fas fa-trash"></i>
        </button>
    `;
    pairs.appendChild(newPair);
}

// Gestion des soumissions de formulaires
async function handleCategorySubmit(event) {
    event.preventDefault();
    console.log('Tentative de soumission du formulaire catégorie');

    const formData = {
        name: document.getElementById('category-name').value,
        description: document.getElementById('category-description').value,
        icon: document.getElementById('category-icon').value
    };

    try {
        const response = await fetch('../api/admin/categories.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('adminToken')}`
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        if (data.success) {
            alert('Catégorie créée avec succès');
            closeModal('category-modal');
            loadCategories(); // Recharger la liste des catégories
        } else {
            alert(data.message || 'Erreur lors de la création de la catégorie');
        }
    } catch (error) {
        console.error('Erreur lors de la création de la catégorie:', error);
        alert('Erreur lors de la création de la catégorie');
    }
}

async function handleElementSubmit(event) {
    event.preventDefault();
    console.log('Tentative de soumission du formulaire élément');

    const type = document.getElementById('element-type').value;
    let content;

    // Récupérer le contenu selon le type
    switch(type) {
        case 'text':
            content = {
                text: document.getElementById('text-content').value
            };
            break;
        case 'table':
            const table = [];
            const rows = document.querySelectorAll('#table-editor tr');
            rows.forEach(row => {
                const cells = Array.from(row.querySelectorAll('input')).map(input => input.value);
                table.push(cells);
            });
            content = {
                table: table
            };
            break;
        case 'list':
            content = {
                items: document.getElementById('list-items').value.split('\n').filter(item => item.trim())
            };
            break;
        case 'image':
        case 'audio':
        case 'video':
            const formData = new FormData();
            formData.append('file', document.getElementById('media-file').files[0]);
            formData.append('description', document.getElementById('media-description').value);
            content = formData;
            break;
        case 'quiz':
            const options = [];
            document.querySelectorAll('.quiz-option').forEach(option => {
                const text = option.querySelector('input[type="text"]').value;
                const isCorrect = option.querySelector('input[type="checkbox"]').checked;
                if (text.trim()) {
                    options.push({ text, isCorrect });
                }
            });
            content = {
                question: document.getElementById('quiz-question').value,
                options: options
            };
            break;
        case 'matching':
            const pairs = [];
            document.querySelectorAll('.matching-pair').forEach(pair => {
                const inputs = pair.querySelectorAll('input[type="text"]');
                const left = inputs[0].value.trim();
                const right = inputs[1].value.trim();
                if (left && right) {
                    pairs.push({ left, right });
                }
            });
            content = {
                pairs: pairs
            };
            break;
    }

    const formData = {
        name: document.getElementById('element-name').value,
        category_id: document.getElementById('element-category').value,
        type: type,
        content: content
    };

    try {
        const response = await fetch('../api/admin/elements.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('adminToken')}`
            },
            body: JSON.stringify(formData)
        });

        const data = await response.json();
        if (data.success) {
            alert('Élément créé avec succès');
            closeModal('element-modal');
            loadElements(); // Recharger la liste des éléments
        } else {
            alert(data.message || 'Erreur lors de la création de l\'élément');
        }
    } catch (error) {
        console.error('Erreur lors de la création de l\'élément:', error);
        alert('Erreur lors de la création de l\'élément');
    }
}

async function handleMediaSubmit(event) {
    event.preventDefault();
    console.log('Tentative de soumission du formulaire média');

    const formData = new FormData();
    formData.append('title', document.getElementById('media-title').value);
    formData.append('file', document.getElementById('media-file').files[0]);
    formData.append('type', document.getElementById('media-type').value);
    formData.append('description', document.getElementById('media-description').value);

    try {
        const response = await fetch('../api/admin/media.php', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('adminToken')}`
            },
            body: formData
        });

        const data = await response.json();
        if (data.success) {
            alert('Média ajouté avec succès');
            closeModal('media-modal');
            loadMedia(); // Recharger la liste des médias
        } else {
            alert(data.message || 'Erreur lors de l\'ajout du média');
        }
    } catch (error) {
        console.error('Erreur lors de l\'ajout du média:', error);
        alert('Erreur lors de l\'ajout du média');
    }
}

// Fonctions de suppression
async function deleteCategory(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')) {
        return;
    }
    
    try {
        const response = await fetch(`../api/admin/categories.php?id=${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('adminToken')}`
            }
        });

        const result = await response.json();
        
        if (result.success) {
            loadCategories();
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error('Erreur lors de la suppression de la catégorie:', error);
        alert('Une erreur est survenue lors de la suppression de la catégorie');
    }
}

async function deleteElement(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
        return;
    }
    
    try {
        const response = await fetch(`../api/admin/elements.php?id=${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('adminToken')}`
            }
        });

        const result = await response.json();
        
        if (result.success) {
            loadElements();
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error('Erreur lors de la suppression de l\'élément:', error);
        alert('Une erreur est survenue lors de la suppression de l\'élément');
    }
}

async function deleteMedia(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce média ?')) {
        return;
    }
    
    try {
        const response = await fetch(`../api/admin/media.php?id=${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('adminToken')}`
            }
        });

        const result = await response.json();
        
        if (result.success) {
            loadMedia();
        } else {
            alert(result.message);
        }
    } catch (error) {
        console.error('Erreur lors de la suppression du média:', error);
        alert('Une erreur est survenue lors de la suppression du média');
    }
}

// Initialisation
document.addEventListener('DOMContentLoaded', () => {
    console.log('Initialisation de l\'interface admin');

    // Attacher les gestionnaires d'événements aux formulaires
    const categoryForm = document.getElementById('category-form');
    if (categoryForm) {
        console.log('Formulaire catégorie trouvé');
        categoryForm.addEventListener('submit', handleCategorySubmit);
    }

    const elementForm = document.getElementById('element-form');
    if (elementForm) {
        console.log('Formulaire élément trouvé');
        elementForm.addEventListener('submit', handleElementSubmit);
    }

    const mediaForm = document.getElementById('media-form');
    if (mediaForm) {
        console.log('Formulaire média trouvé');
        mediaForm.addEventListener('submit', handleMediaSubmit);
    }

    // Charger les données initiales
    loadCategories();
    loadElements();
    loadMedia();
}); 