// Content Management Functions
const contentManager = {
    // Add new content
    addContent: async (contentData) => {
        try {
            const response = await fetch('/api/content.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(contentData)
            });
            
            const result = await response.json();
            if (result.success) {
                showSuccess(result.message);
                // Refresh content list
                contentManager.loadContent();
                return true;
            } else {
                showError(result.message);
                return false;
            }
        } catch (error) {
            console.error('Error adding content:', error);
            showError('Erreur lors de l\'ajout du contenu');
            return false;
        }
    },

    // Load content list
    loadContent: async () => {
        try {
            const response = await fetch('/api/content.php');
            const result = await response.json();
            
            if (result.success) {
                const contentList = document.getElementById('content-list');
                if (contentList) {
                    contentList.innerHTML = result.content.map(content => `
                        <div class="content-item" data-id="${content.id}">
                            <h3>${content.title}</h3>
                            <p>${content.description}</p>
                            <div class="content-meta">
                                <span>Type: ${content.content_type}</span>
                                <span>Catégorie: ${content.category_name || 'Non spécifié'}</span>
                                <span>Créé par: ${content.creator_name}</span>
                                ${content.is_approved ? '' : '<span class="pending">En attente d\'approbation</span>'}
                            </div>
                            ${isAdmin() ? `
                                <div class="content-actions">
                                    ${!content.is_approved ? `
                                        <button onclick="contentManager.approveContent(${content.id})" class="btn btn-success">
                                            Approuver
                                        </button>
                                    ` : ''}
                                    <button onclick="contentManager.deleteContent(${content.id})" class="btn btn-danger">
                                        Supprimer
                                    </button>
                                </div>
                            ` : ''}
                        </div>
                    `).join('');
                }
            } else {
                showError(result.message);
            }
        } catch (error) {
            console.error('Error loading content:', error);
            showError('Erreur lors du chargement du contenu');
        }
    },

    // Approve content (admin only)
    approveContent: async (contentId) => {
        if (!isAdmin()) {
            showError('Non autorisé');
            return;
        }

        try {
            const response = await fetch('/api/content.php', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ content_id: contentId })
            });
            
            const result = await response.json();
            if (result.success) {
                showSuccess(result.message);
                contentManager.loadContent();
            } else {
                showError(result.message);
            }
        } catch (error) {
            console.error('Error approving content:', error);
            showError('Erreur lors de l\'approbation du contenu');
        }
    },

    // Delete content (admin only)
    deleteContent: async (contentId) => {
        if (!isAdmin()) {
            showError('Non autorisé');
            return;
        }

        if (!confirm('Êtes-vous sûr de vouloir supprimer ce contenu ?')) {
            return;
        }

        try {
            const response = await fetch(`/api/content.php?id=${contentId}`, {
                method: 'DELETE'
            });
            
            const result = await response.json();
            if (result.success) {
                showSuccess(result.message);
                contentManager.loadContent();
            } else {
                showError(result.message);
            }
        } catch (error) {
            console.error('Error deleting content:', error);
            showError('Erreur lors de la suppression du contenu');
        }
    }
};

// Helper functions
function showSuccess(message) {
    // Implement your success message display
    alert(message);
}

function showError(message) {
    // Implement your error message display
    alert(message);
}

function isAdmin() {
    // Check if current user is admin
    return document.body.dataset.userRole === 'admin';
}

// Initialize content management when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Load content list if on content management page
    if (document.getElementById('content-list')) {
        contentManager.loadContent();
    }

    // Handle content form submission
    const contentForm = document.getElementById('content-form');
    if (contentForm) {
        contentForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = {
                title: document.getElementById('title').value,
                description: document.getElementById('description').value,
                category_id: document.getElementById('category').value,
                content_type: document.getElementById('content_type').value,
                content_url: document.getElementById('content_url').value,
                age_range_min: document.getElementById('age_min').value,
                age_range_max: document.getElementById('age_max').value
            };

            await contentManager.addContent(formData);
            contentForm.reset();
        });
    }
}); 