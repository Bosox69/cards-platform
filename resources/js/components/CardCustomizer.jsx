import React, { useState, useEffect } from 'react';
import ReactDOM from 'react-dom/client';
import axios from 'axios';

function CardCustomizer({ departmentId, initialTemplateId }) {
    const [templates, setTemplates] = useState([]);
    const [selectedTemplate, setSelectedTemplate] = useState(null);
    const [loading, setLoading] = useState(true);
    const [formData, setFormData] = useState({
        fullName: '',
        jobTitle: '',
        email: '',
        phone: '',
        mobile: '',
        address: '',
        quantity: '100',
        isDoubleSided: false
    });
    const [previewUrl, setPreviewUrl] = useState(null);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState(null);
    const [success, setSuccess] = useState(null);

    // Charger les modèles du département
    useEffect(() => {
        if (departmentId) {
            setLoading(true);
            axios.get(`/api/departments/${departmentId}/templates`)
                .then(response => {
                    setTemplates(response.data);
                    if (response.data.length > 0) {
                        const templateToSelect = initialTemplateId
                            ? response.data.find(t => t.id === parseInt(initialTemplateId))
                            : response.data[0];
                        
                        if (templateToSelect) {
                            setSelectedTemplate(templateToSelect);
                        }
                    }
                    setLoading(false);
                })
                .catch(error => {
                    console.error('Error loading templates:', error);
                    setError('Impossible de charger les modèles. Veuillez réessayer.');
                    setLoading(false);
                });
        }
    }, [departmentId, initialTemplateId]);

    // Gérer les changements de champs
    const handleInputChange = (e) => {
        const { name, value, type, checked } = e.target;
        setFormData({
            ...formData,
            [name]: type === 'checkbox' ? checked : value
        });
    };

    // Gérer le changement de modèle
    const handleTemplateChange = (e) => {
        const templateId = parseInt(e.target.value);
        const template = templates.find(t => t.id === templateId);
        setSelectedTemplate(template);
    };

    // Générer la prévisualisation
    const generatePreview = async () => {
        try {
            setError(null);
            const response = await axios.post('/api/preview-card', {
                template_id: selectedTemplate.id,
                is_double_sided: formData.isDoubleSided,
                card_data: {
                    fullName: formData.fullName,
                    jobTitle: formData.jobTitle,
                    email: formData.email,
                    phone: formData.phone,
                    mobile: formData.mobile,
                    address: formData.address
                }
            });
            
            setPreviewUrl(response.data.preview_url);
        } catch (error) {
            console.error('Error generating preview:', error);
            setError('Impossible de générer la prévisualisation. Veuillez réessayer.');
        }
    };

    // Ajouter au panier
    const addToCart = async (e) => {
        e.preventDefault();
        
        if (!formData.fullName || !formData.jobTitle) {
            setError('Veuillez remplir au moins le nom et le titre.');
            return;
        }
        
        try {
            setIsSubmitting(true);
            setError(null);
            
            const response = await axios.post('/client/orders/add-to-cart', {
                template_id: selectedTemplate.id,
                quantity: parseInt(formData.quantity),
                is_double_sided: formData.isDoubleSided,
                card_data: {
                    fullName: formData.fullName,
                    jobTitle: formData.jobTitle,
                    email: formData.email,
                    phone: formData.phone,
                    mobile: formData.mobile,
                    address: formData.address
                }
            });
            
            setSuccess('Carte ajoutée au panier avec succès !');
            
            // Mettre à jour le compteur de panier dans l'interface
            const cartCountElement = document.querySelector('#cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = response.data.cart.length;
            }
            
            // Réinitialiser le formulaire après quelques secondes
            setTimeout(() => {
                setFormData({
                    fullName: '',
                    jobTitle: '',
                    email: '',
                    phone: '',
                    mobile: '',
                    address: '',
                    quantity: '100',
                    isDoubleSided: false
                });
                setPreviewUrl(null);
                setSuccess(null);
            }, 3000);
            
        } catch (error) {
            console.error('Error adding to cart:', error);
            setError('Impossible d\'ajouter au panier. Veuillez réessayer.');
        } finally {
            setIsSubmitting(false);
        }
    };

    if (loading) {
        return <div className="flex justify-center p-6"><div className="spinner"></div></div>;
    }

    if (!templates.length) {
        return <div className="alert alert-warning">Aucun modèle disponible pour ce département.</div>;
    }

    return (
        <div className="card-customizer">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="form-container">
                    <h2 className="text-lg font-semibold mb-4">Personnalisation de la carte</h2>
                    
                    {error && (
                        <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span className="block sm:inline">{error}</span>
                        </div>
                    )}
                    
                    {success && (
                        <div className="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span className="block sm:inline">{success}</span>
                        </div>
                    )}
                    
                    <form onSubmit={addToCart} className="space-y-4">
                        <div className="form-group">
                            <label htmlFor="template" className="block mb-1">Modèle</label>
                            <select
                                id="template"
                                className="w-full p-2 border rounded"
                                value={selectedTemplate ? selectedTemplate.id : ''}
                                onChange={handleTemplateChange}
                            >
                                {templates.map(template => (
                                    <option key={template.id} value={template.id}>
                                        {template.name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div className="form-group">
                            <label htmlFor="fullName" className="block mb-1">Nom complet *</label>
                            <input
                                type="text"
                                id="fullName"
                                name="fullName"
                                className="w-full p-2 border rounded"
                                value={formData.fullName}
                                onChange={handleInputChange}
                                required
                            />
                        </div>

                        <div className="form-group">
                            <label htmlFor="jobTitle" className="block mb-1">Titre / Fonction *</label>
                            <input
                                type="text"
                                id="jobTitle"
                                name="jobTitle"
                                className="w-full p-2 border rounded"
                                value={formData.jobTitle}
                                onChange={handleInputChange}
                                required
                            />
                        </div>

                        <div className="form-group">
                            <label htmlFor="email" className="block mb-1">Email</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                className="w-full p-2 border rounded"
                                value={formData.email}
                                onChange={handleInputChange}
                            />
                        </div>

                        <div className="form-group">
                            <label htmlFor="phone" className="block mb-1">Téléphone fixe</label>
                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                className="w-full p-2 border rounded"
                                value={formData.phone}
                                onChange={handleInputChange}
                            />
                        </div>

                        <div className="form-group">
                            <label htmlFor="mobile" className="block mb-1">Mobile</label>
                            <input
                                type="tel"
                                id="mobile"
                                name="mobile"
                                className="w-full p-2 border rounded"
                                value={formData.mobile}
                                onChange={handleInputChange}
                            />
                        </div>

                        <div className="form-group">
                            <label htmlFor="address" className="block mb-1">Adresse</label>
                            <textarea
                                id="address"
                                name="address"
                                className="w-full p-2 border rounded"
                                rows="3"
                                value={formData.address}
                                onChange={handleInputChange}
                            ></textarea>
                        </div>

                        <div className="form-group">
                            <label htmlFor="quantity" className="block mb-1">Quantité</label>
                            <select
                                id="quantity"
                                name="quantity"
                                className="w-full p-2 border rounded"
                                value={formData.quantity}
                                onChange={handleInputChange}
                            >
                                <option value="100">100 exemplaires</option>
                                <option value="200">200 exemplaires</option>
                                <option value="400">400 exemplaires</option>
                            </select>
                        </div>

                        <div className="form-group">
                            <label className="flex items-center">
                                <input
                                    type="checkbox"
                                    name="isDoubleSided"
                                    className="mr-2"
                                    checked={formData.isDoubleSided}
                                    onChange={handleInputChange}
                                />
                                Impression recto-verso
                            </label>
                        </div>

                        <div className="flex space-x-4">
                            <button
                                type="button"
                                className="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300"
                                onClick={generatePreview}
                            >
                                Prévisualiser
                            </button>
                            
                            <button
                                type="submit"
                                className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 disabled:opacity-50"
                                disabled={isSubmitting}
                            >
                                {isSubmitting ? 'Ajout en cours...' : 'Ajouter au panier'}
                            </button>
                        </div>
                    </form>
                </div>

                <div className="preview-container">
                    <h2 className="text-lg font-semibold mb-4">Prévisualisation</h2>
                    
                    {previewUrl ? (
                        <div className="preview-card border rounded p-4">
                            <iframe 
                                src={previewUrl} 
                                className="w-full h-96 border-0" 
                                title="Prévisualisation de la carte"
                            ></iframe>
                            
                            <div className="mt-4">
                                <a 
                                    href={previewUrl} 
                                    target="_blank" 
                                    className="text-blue-500 hover:underline"
                                    rel="noopener noreferrer"
                                >
                                    Ouvrir la prévisualisation dans un nouvel onglet
                                </a>
                            </div>
                        </div>
                    ) : (
                        <div className="flex items-center justify-center h-96 bg-gray-100 rounded border">
                            <p className="text-gray-500">Cliquez sur "Prévisualiser" pour voir un aperçu de votre carte</p>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}

if (document.getElementById('card-customizer')) {
    const element = document.getElementById('card-customizer');
    const root = ReactDOM.createRoot(element);
    root.render(
        <CardCustomizer
            departmentId={element.dataset.departmentId}
            initialTemplateId={element.dataset.templateId}
        />
    );
}

export default CardCustomizer;
