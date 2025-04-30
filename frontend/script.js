// API Configuration
const API_BASE_URL = 'http://localhost:8000/api';
const RECIPES_API = `${API_BASE_URL}/recipes.php`;

// === Logout User ===
function logout() {
  // Clear the stored user data
  localStorage.removeItem('currentUser');
  localStorage.removeItem('userId');
  
  // Clear any user-specific data
  const favoriteList = document.getElementById('favoriteList');
  const journalEntries = document.getElementById('journalEntries');
  const mealPlanContainer = document.getElementById('mealPlanContainer');
  
  if (favoriteList) favoriteList.innerHTML = '';
  if (journalEntries) journalEntries.innerHTML = '';
  if (mealPlanContainer) mealPlanContainer.innerHTML = '';
  
  // Redirect to home page
  window.location.href = 'index.html';
}

// Show login modal
function showLoginModal() {
  console.log('Showing login modal');
  document.getElementById('welcomeModal').style.display = 'none';
  document.getElementById('loginModal').style.display = 'block';
}

// Show register modal
function showRegisterModal() {
  console.log('Showing register modal');
  document.getElementById('welcomeModal').style.display = 'none';
  document.getElementById('registerModal').style.display = 'block';
}

// Back to welcome modal
function backToWelcome() {
  console.log('Going back to welcome modal');
  document.getElementById('loginModal').style.display = 'none';
  document.getElementById('registerModal').style.display = 'none';
  document.getElementById('welcomeModal').style.display = 'block';
}

// === Register User ===
function register() {
  console.log('Register function called');
  const username = document.getElementById('registerUsername').value.trim();
  const password = document.getElementById('registerPassword').value.trim();
  const confirmPassword = document.getElementById('confirmPassword').value.trim();

  console.log('Registration attempt:', { username, password, confirmPassword });

  if (!username || !password || !confirmPassword) {
    alert('Please fill in all fields!');
    return;
  }

  if (password !== confirmPassword) {
    alert('Passwords do not match!');
    return;
  }

  fetch(`${API_BASE_URL}/users.php?action=register`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username, password })
  })
  .then(res => {
    if (!res.ok) {
      return res.json().then(data => {
        throw new Error(data.error || 'Registration failed');
      });
    }
    return res.json();
  })
  .then(data => {
    console.log('Registration response:', data);
    if (data.message === 'User registered successfully') {
      // Automatically log in the user
      fetch(`${API_BASE_URL}/users.php?action=login`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ username, password })
      })
      .then(res => {
        if (!res.ok) {
          return res.json().then(data => {
            throw new Error(data.error || 'Login failed');
          });
        }
        return res.json();
      })
      .then(data => {
        console.log('Login response:', data);
        if (data.message === 'Login successful!') {
          // Store user ID and username
          localStorage.setItem('userId', data.user_id);
          localStorage.setItem('currentUser', username);
          
          // Show success message
          alert('Registration and login successful!');
          
          // Hide all modals
          document.getElementById('welcomeModal').style.display = 'none';
          document.getElementById('loginModal').style.display = 'none';
          document.getElementById('registerModal').style.display = 'none';
          
          // Update login status display
          const loginStatus = document.getElementById('loginStatus');
          const userDisplay = document.getElementById('userDisplay');
          
          userDisplay.textContent = `Logged in as: ${username}`;
          loginStatus.style.display = 'flex';
          
          // Reload any user-specific data
          loadFavorites();
          loadJournalEntries();
          loadMealPlans();
        } else {
          throw new Error('Login failed. Please try logging in manually.');
        }
      })
      .catch(error => {
        console.error('Login error:', error);
        alert(error.message || 'An error occurred during login. Please try logging in manually.');
      });
    } else {
      throw new Error('Registration failed. Please try again.');
    }
  })
  .catch(error => {
    console.error('Registration error:', error);
    alert(error.message || 'An error occurred during registration. Please try again.');
  });
}

// === Login User ===
function login() {
  console.log('Login function called');
  const username = document.getElementById('loginUsername').value.trim();
  const password = document.getElementById('loginPassword').value.trim();

  console.log('Login attempt:', { username, password });

  if (!username || !password) {
    alert('Please fill in all fields!');
    return;
  }

  fetch(`${API_BASE_URL}/users.php?action=login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ username, password })
  })
  .then(res => {
    if (!res.ok) {
      return res.json().then(data => {
        throw new Error(data.error || 'Login failed');
      });
    }
    return res.json();
  })
  .then(data => {
    console.log('Login response:', data);
    if (data.message === 'Login successful!' && data.user_id) {
      // Store user ID and username
      localStorage.setItem('userId', data.user_id);
      localStorage.setItem('currentUser', username);
      
      // Show success message
      alert('Login successful!');
      
      // Hide all modals
      document.getElementById('welcomeModal').style.display = 'none';
      document.getElementById('loginModal').style.display = 'none';
      document.getElementById('registerModal').style.display = 'none';
      
      // Update login status display
      const loginStatus = document.getElementById('loginStatus');
      const userDisplay = document.getElementById('userDisplay');
      
      userDisplay.textContent = `Logged in as: ${username}`;
      loginStatus.style.display = 'flex';
      
      // Reload any user-specific data
      loadFavorites();
      loadJournalEntries();
      loadMealPlans();
    } else {
      throw new Error('Login failed. Please check your credentials.');
    }
  })
  .catch(error => {
    console.error('Login error:', error);
    alert(error.message || 'An error occurred during login. Please try again.');
  });
}

// Check for existing login on page load
window.addEventListener('load', () => {
  const currentUser = localStorage.getItem('currentUser');
  const userId = localStorage.getItem('userId');
  
  if (currentUser && userId) {
    // User is already logged in
    document.getElementById('welcomeModal').style.display = 'none';
    document.getElementById('loginModal').style.display = 'none';
    document.getElementById('registerModal').style.display = 'none';
    
    const loginStatus = document.getElementById('loginStatus');
    const userDisplay = document.getElementById('userDisplay');
    
    userDisplay.textContent = `Logged in as: ${currentUser}`;
    loginStatus.style.display = 'flex';
    
    // Load user-specific data
    loadFavorites();
    loadJournalEntries();
    loadMealPlans();
  } else {
    // Show welcome modal for new users
    document.getElementById('welcomeModal').style.display = 'block';
    const loginStatus = document.getElementById('loginStatus');
    loginStatus.style.display = 'none';
  }
});

// === Search Recipes ===
const FAVORITES_API = `${API_BASE_URL}/favorites.php`;
const JOURNAL_API = `${API_BASE_URL}/journal.php`;
const USERS_API = `${API_BASE_URL}/users.php`;

async function searchRecipes() {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const query = searchInput.value.trim();

    if (!query) {
        searchResults.innerHTML = '<p>Please enter a search term</p>';
        return;
    }

    searchResults.innerHTML = '<div class="loading">Searching recipes...</div>';

    try {
        const response = await fetch(`${API_BASE_URL}/google_search.php?query=${encodeURIComponent(query)}`);
        const results = await response.json();

        if (results.error) {
            throw new Error(results.error);
        }

        if (results.length === 0) {
            searchResults.innerHTML = '<p>No recipes found. Try a different search term.</p>';
            return;
        }

        searchResults.innerHTML = results.map(recipe => `
            <div class="recipe-card">
                ${recipe.thumbnail ? `<img src="${recipe.thumbnail}" alt="${recipe.title}">` : ''}
                <h3>${recipe.title}</h3>
                <p>${recipe.snippet}</p>
                <a href="${recipe.link}" target="_blank" class="btn">View Recipe</a>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error searching recipes:', error);
        searchResults.innerHTML = `<p>Error searching recipes: ${error.message}</p>`;
    }
}

async function getRandomRecipe() {
    try {
        showLoadingSpinner();
        const response = await fetch(`${RECIPES_API}?action=random`);
        if (!response.ok) {
            throw new Error('Failed to fetch random recipe');
        }
        const recipe = await response.json();
        if (!recipe || Object.keys(recipe).length === 0) {
            throw new Error('No recipe found');
        }
        displayRecipes([recipe]);
    } catch (error) {
        console.error('Random recipe error:', error);
        alert('Error finding random recipe: ' + error.message);
    } finally {
        hideLoadingSpinner();
    }
}

function showLoadingSpinner() {
    const spinner = document.getElementById('loading-spinner');
    if (spinner) spinner.style.display = 'block';
}

function hideLoadingSpinner() {
    const spinner = document.getElementById('loading-spinner');
    if (spinner) spinner.style.display = 'none';
}

function displayRecipes(recipes) {
    const container = document.getElementById('recipe-results');
    container.innerHTML = '';

    if (!Array.isArray(recipes)) {
        recipes = [recipes];
    }

    recipes.forEach(recipe => {
        const recipeCard = document.createElement('div');
        recipeCard.className = 'recipe-card';

        const ingredients = Array.isArray(recipe.ingredients) 
            ? recipe.ingredients.join('\n') 
            : recipe.ingredients;

        const instructions = Array.isArray(recipe.instructions)
            ? recipe.instructions.join('\n')
            : recipe.instructions;

        recipeCard.innerHTML = `
            <h3>${recipe.name}</h3>
            <div class="recipe-content">
                <h4>Ingredients:</h4>
                <p>${ingredients}</p>
                <h4>Instructions:</h4>
                <p>${instructions}</p>
            </div>
            <button onclick="saveRecipeToFavorites(${recipe.id})">
                Save to Favorites
            </button>
        `;
        container.appendChild(recipeCard);
    });
}

// Event listeners
document.getElementById('search-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    searchRecipes();
});

document.getElementById('surprise-me')?.addEventListener('click', function(e) {
    e.preventDefault();
    getRandomRecipe();
});

// === Save to Favorites ===
function saveRecipeToFavorites(recipeId) {
  const userId = localStorage.getItem('userId');
  if (!userId) {
    document.getElementById('favoriteList').innerHTML = '<p>Please log in to save recipes to favorites</p>';
    return;
  }

  fetch(`${FAVORITES_API}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ 
      recipe_id: recipeId,
      user_id: userId
    })
  })
  .then(res => {
    if (!res.ok) {
      return res.json().then(data => {
        throw new Error(data.error || 'Failed to save recipe');
      });
    }
    return res.json();
  })
  .then(data => {
    alert(data.message || 'Recipe saved to favorites!');
    loadFavorites(); // Refresh the favorites list
    // Refresh the recipe dropdown in meal plans
    if (document.getElementById('recipeSelect')) {
      loadRecipes();
    }
  })
  .catch(error => {
    console.error('Error saving recipe:', error);
    document.getElementById('favoriteList').innerHTML = 
      `<p class="error">Error saving recipe: ${error.message}</p>`;
  });
}

// Add function to delete favorites
async function removeFromFavorites(favoriteId) {
    try {
        const response = await fetch(`${API_BASE_URL}/favorites.php`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                favorite_id: favoriteId,
                user_id: localStorage.getItem('userId')
            })
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.error || 'Failed to remove from favorites');
        }

        // Refresh the favorites list
        loadFavorites();
    } catch (error) {
        console.error('Error removing from favorites:', error);
        alert(error.message);
    }
}

// === Load Favorites ===
async function loadFavorites() {
    try {
        const userId = localStorage.getItem('userId');
        if (!userId) {
            document.getElementById('favoriteList').innerHTML = '<p>Please log in to view your favorites</p>';
            return;
        }

        const response = await fetch(`${API_BASE_URL}/favorites.php?user_id=${userId}`);
        if (!response.ok) {
            throw new Error('Failed to load favorites');
        }

        const favorites = await response.json();
        const favoriteList = document.getElementById('favoriteList');
        
        if (favorites.length === 0) {
            favoriteList.innerHTML = '<p>No favorite recipes yet</p>';
            return;
        }

        favoriteList.innerHTML = favorites.map(favorite => `
            <div class="favorite-item">
                <h3>${favorite.recipe_name}</h3>
                <p><strong>Ingredients:</strong> ${favorite.ingredients}</p>
                <p><strong>Instructions:</strong> ${favorite.instructions}</p>
                <button onclick="removeFavorite(${favorite.favorite_id})">Remove from Favorites</button>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error loading favorites:', error);
        document.getElementById('favoriteList').innerHTML = '<p>Error loading favorites. Please try again later.</p>';
    }
}

async function removeFavorite(favoriteId) {
    try {
        const userId = localStorage.getItem('userId');
        if (!userId) {
            alert('Please log in to remove favorites');
            return;
        }

        const response = await fetch(`${API_BASE_URL}/favorites.php`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                favorite_id: favoriteId,
                user_id: userId
            })
        });

        if (!response.ok) {
            throw new Error('Failed to remove favorite');
        }

        // Reload the favorites list
        loadFavorites();
    } catch (error) {
        console.error('Error removing favorite:', error);
        alert('Failed to remove favorite. Please try again later.');
    }
}

// === Save Journal ===
function saveJournal() {
  const title = document.getElementById('journalTitle').value.trim();
  const content = document.getElementById('journalContent').value.trim();
  const userId = localStorage.getItem('userId');

  if (!userId) {
    document.getElementById('journalEntries').innerHTML = '<p>Please log in to save journal entries</p>';
    return;
  }

  if (!title || !content) {
    alert('Please fill in both title and content');
    return;
  }

  fetch(`${JOURNAL_API}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ 
        title, 
        content,
        user_id: userId
      })
  })
  .then(res => {
    if (!res.ok) {
      return res.json().then(data => {
        throw new Error(data.error || 'Failed to save journal entry');
      });
    }
    return res.json();
  })
  .then(data => {
    alert(data.message || 'Entry saved successfully!');
    // Clear the form
    document.getElementById('journalTitle').value = '';
    document.getElementById('journalContent').value = '';
    // Load all journal entries
    loadJournalEntries();
    // Refresh the recipe dropdown in meal plans
    if (document.getElementById('recipeSelect')) {
      loadRecipes();
    }
  })
  .catch(error => {
    console.error('Error saving journal entry:', error);
    document.getElementById('journalEntries').innerHTML = 
      `<p class="error">Error saving journal entry: ${error.message}</p>`;
  });
}

// === Load Journal Entries ===
function loadJournalEntries() {
  const userId = localStorage.getItem('userId');
  if (!userId) {
    document.getElementById('journalEntries').innerHTML = '<p>Please log in to view journal entries</p>';
    return;
  }

  fetch(`${JOURNAL_API}?user_id=${userId}`)
    .then(res => {
      if (!res.ok) {
        return res.json().then(data => {
          throw new Error(data.error || 'Failed to load journal entries');
        });
      }
      return res.json();
    })
    .then(entries => {
      const list = document.getElementById('journalEntries');
      list.innerHTML = '';
      
      if (entries.length === 0) {
        list.innerHTML = '<p>No journal entries yet</p>';
        return;
      }

      entries.forEach(entry => {
        const li = document.createElement('li');
        const date = entry.created_at ? new Date(entry.created_at).toLocaleString() : 'No date available';
        li.innerHTML = `
          <div class="journal-entry">
            <strong>${entry.title}</strong>
            <p>${entry.content}</p>
            <small>Created: ${date}</small>
            <button onclick="saveJournalToFavorites(${entry.id}, '${entry.title.replace(/'/g, "\\'")}', '${entry.content.replace(/'/g, "\\'")}')" class="favorite-btn">
              Save to Favorites
            </button>
          </div>
        `;
        list.appendChild(li);
      });
    })
    .catch(error => {
      console.error('Error loading journal entries:', error);
      document.getElementById('journalEntries').innerHTML = 
        `<p class="error">Error loading journal entries: ${error.message}</p>`;
    });
}

// Add new function to save journal entry to favorites
async function saveJournalToFavorites(entryId, title, content) {
    try {
        const userId = localStorage.getItem('userId');
        if (!userId) {
            alert('Please log in to save recipes to favorites');
            return;
        }

        // First create a recipe from the journal entry
        const recipeResponse = await fetch(`${API_BASE_URL}/recipes.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                name: title,
                ingredients: content,
                instructions: content,
                user_id: userId
            })
        });

        if (!recipeResponse.ok) {
            const error = await recipeResponse.json();
            throw new Error(error.error || 'Failed to create recipe');
        }

        const recipeData = await recipeResponse.json();
        
        // Then save the recipe to favorites
        const favoriteResponse = await fetch(`${API_BASE_URL}/favorites.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                recipe_id: recipeData.id,
                user_id: userId
            })
        });

        if (!favoriteResponse.ok) {
            const error = await favoriteResponse.json();
            throw new Error(error.error || 'Failed to save to favorites');
        }

        alert('Recipe saved to favorites!');
        loadFavorites(); // Refresh the favorites list
    } catch (error) {
        console.error('Error saving recipe:', error);
        alert(error.message);
    }
}

// Load journal entries when the page loads
window.addEventListener('load', () => {
  const currentUser = localStorage.getItem('currentUser');
  if (currentUser) {
    loadJournalEntries();
  }
});

// === Ask AI Assistant ===
function askAI() {
  const prompt = document.getElementById('aiPrompt').value.trim();

  fetch(`${API_BASE_URL}/backend/api/ai_assistant.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ prompt })
  })
  .then(res => res.json())
  .then(data => {
      document.getElementById('aiResponse').innerText = data.response || 'No response from AI';
  });
}

// Meal Plans
function loadMealPlans() {
    const userId = localStorage.getItem('userId');
    if (!userId) {
        showMessage('Please log in to view meal plans', 'error');
        return;
    }

    fetch(`${API_BASE_URL}/meal_plans.php?action=get&user_id=${userId}`)
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.error || 'Failed to load meal plans');
                });
            }
            return response.json();
        })
        .then(meals => {
            // Clear existing meal slots
            document.querySelectorAll('.meal-slot').forEach(slot => {
                const mealType = slot.getAttribute('data-meal');
                slot.innerHTML = `<h4>${mealType}</h4>`;
            });

            // Add meals to their respective slots
            meals.forEach(meal => {
                const dayColumn = document.getElementById(meal.day);
                if (dayColumn) {
                    const mealSlot = dayColumn.querySelector(`.meal-slot[data-meal="${meal.meal_type}"]`);
                    if (mealSlot) {
                        const mealContent = document.createElement('div');
                        mealContent.className = 'meal-content';
                        mealContent.innerHTML = `
                            <div class="meal-item">
                                <span>${meal.recipe_name}</span>
                                <button class="delete-btn" onclick="deleteMeal(${meal.id})">×</button>
                            </div>
                        `;
                        mealSlot.appendChild(mealContent);
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error loading meal plans:', error);
            showMessage('Error loading meal plans: ' + error.message, 'error');
        });
}

// Add a meal to the plan
function addMeal() {
    const userId = localStorage.getItem('userId');
    if (!userId) {
        showMessage('Please log in to add meals to your plan', 'error');
        return;
    }

    const recipeSelect = document.getElementById('recipeSelect');
    const daySelect = document.getElementById('daySelect');
    const mealTypeSelect = document.getElementById('mealTypeSelect');

    if (!recipeSelect.value || !daySelect.value || !mealTypeSelect.value) {
        showMessage('Please select a recipe, day, and meal type', 'error');
        return;
    }

    const recipeName = recipeSelect.options[recipeSelect.selectedIndex].text.replace(/^[📖⭐] /, '');

    const mealData = {
        user_id: userId,
        recipe_id: recipeSelect.value,
        recipe_name: recipeName,
        day: daySelect.value.toLowerCase(),
        meal_type: mealTypeSelect.value.toLowerCase()
    };

    fetch(`${API_BASE_URL}/meal_plans.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(mealData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.error || 'Failed to add meal to plan');
            });
        }
        return response.json();
    })
    .then(data => {
        showMessage('Meal added to plan successfully', 'success');
        loadMealsForWeek();
    })
    .catch(error => {
        console.error('Error adding meal:', error);
        showMessage(error.message || 'Failed to add meal to plan', 'error');
    });
}

// Delete a meal from the plan
function deleteMeal(mealId) {
    if (!confirm('Are you sure you want to delete this meal from your plan?')) {
        return;
    }

    const userId = localStorage.getItem('userId');
    if (!userId) {
        alert('Please log in to delete meals from your plan');
        return;
    }

    fetch(`${API_BASE_URL}/meal_plans.php?id=${mealId}&user_id=${userId}`, {
        method: 'DELETE'
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.error || 'Failed to delete meal');
            });
        }
        return response.json();
    })
    .then(data => {
        // Find and remove the meal from the grid
        const mealContent = document.querySelector(`.meal-content button[onclick="deleteMeal(${mealId})"]`).parentElement;
        if (mealContent) {
            mealContent.remove();
        }
        showMessage('Meal removed from plan successfully', 'success');
    })
    .catch(error => {
        console.error('Error deleting meal:', error);
        showMessage('Error deleting meal: ' + error.message, 'error');
    });
}

// Show message function
function showMessage(message, type) {
    const messageDiv = document.getElementById('message');
    if (messageDiv) {
        messageDiv.textContent = message;
        messageDiv.className = `message ${type}`;
        messageDiv.style.display = 'block';
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 3000);
    }
}

// Load all recipes for the dropdown
function loadRecipes() {
    const userId = localStorage.getItem('userId');
    if (!userId) {
        document.getElementById('recipeSelect').innerHTML = '<option value="">Please log in to view recipes</option>';
        return;
    }

    const select = document.getElementById('recipeSelect');
    select.innerHTML = '<option value="">Select a recipe...</option>';

    // Load user's recipes
    fetch(`${API_BASE_URL}/recipes.php?action=get&user_id=${userId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load recipes');
            }
            return response.json();
        })
        .then(recipes => {
            if (recipes && recipes.length > 0) {
                const recipesGroup = document.createElement('optgroup');
                recipesGroup.label = 'My Recipes';
                recipes.forEach(recipe => {
                    const option = document.createElement('option');
                    option.value = recipe.id;
                    option.textContent = `📖 ${recipe.name || 'Unnamed Recipe'}`;
                    recipesGroup.appendChild(option);
                });
                select.appendChild(recipesGroup);
            }

            // Load favorites
            return fetch(`${API_BASE_URL}/favorites.php?user_id=${userId}`);
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load favorites');
            }
            return response.json();
        })
        .then(favorites => {
            if (favorites && favorites.length > 0) {
                const favGroup = document.createElement('optgroup');
                favGroup.label = 'Favorite Recipes';
                favorites.forEach(favorite => {
                    const option = document.createElement('option');
                    option.value = favorite.recipe_id;
                    option.textContent = `⭐ ${favorite.recipe_name || 'Unnamed Recipe'}`;
                    favGroup.appendChild(option);
                });
                select.appendChild(favGroup);
            }

            // Load journal entries
            return fetch(`${API_BASE_URL}/journal.php?user_id=${userId}`);
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load journal entries');
            }
            return response.json();
        })
        .then(journalEntries => {
            if (journalEntries && journalEntries.length > 0) {
                const journalGroup = document.createElement('optgroup');
                journalGroup.label = 'Journal Entries';
                journalEntries.forEach(entry => {
                    const option = document.createElement('option');
                    option.value = entry.id;
                    option.textContent = `📝 ${entry.title || 'Untitled Entry'}`;
                    journalGroup.appendChild(option);
                });
                select.appendChild(journalGroup);
            }

            if ((!recipes || recipes.length === 0) && (!favorites || favorites.length === 0) && (!journalEntries || journalEntries.length === 0)) {
                const option = document.createElement('option');
                option.disabled = true;
                option.textContent = 'No recipes found. Add some recipes, favorites, or journal entries first.';
                select.appendChild(option);
            }
        })
        .catch(error => {
            console.error('Error loading recipes:', error);
            select.innerHTML = `<option value="" disabled>Error loading recipes: ${error.message}</option>`;
        });
}

// Load recipes when the page loads
document.addEventListener('DOMContentLoaded', function() {
    loadRecipes();
    loadMealPlans();
});

// Mobile Navigation
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    
    if (hamburger) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('active');
        });
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.navbar')) {
            hamburger.classList.remove('active');
            navLinks.classList.remove('active');
        }
    });

    // Close mobile menu when clicking a link
    const navItems = document.querySelectorAll('.nav-links a');
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            hamburger.classList.remove('active');
            navLinks.classList.remove('active');
        });
    });
});

// Scroll Animation for Gallery
function handleScrollAnimation() {
    const galleryItems = document.querySelectorAll('.gallery-item');
    const triggerBottom = window.innerHeight * 0.8;

    galleryItems.forEach(item => {
        const itemTop = item.getBoundingClientRect().top;

        if (itemTop < triggerBottom) {
            item.classList.add('visible');
        }
    });
}

// Initialize scroll animation
document.addEventListener('DOMContentLoaded', function() {
    // Initial check for elements in view
    handleScrollAnimation();

    // Add scroll event listener
    window.addEventListener('scroll', handleScrollAnimation);
});

let currentWeekStart = new Date();
currentWeekStart.setHours(0, 0, 0, 0);
currentWeekStart.setDate(currentWeekStart.getDate() - currentWeekStart.getDay());

function initializeCalendar() {
    const calendarGrid = document.getElementById('calendarGrid');
    calendarGrid.innerHTML = '';

    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const mealTypes = ['Breakfast', 'Lunch', 'Dinner'];

    days.forEach(day => {
        const dayColumn = document.createElement('div');
        dayColumn.className = 'day-column';

        const dayHeader = document.createElement('div');
        dayHeader.className = 'day-header';
        dayHeader.textContent = day;
        dayColumn.appendChild(dayHeader);

        mealTypes.forEach(mealType => {
            const mealSlot = document.createElement('div');
            mealSlot.className = 'meal-slot';
            mealSlot.id = `${day.toLowerCase()}-${mealType.toLowerCase()}`;

            const mealHeader = document.createElement('h4');
            mealHeader.textContent = mealType;
            mealSlot.appendChild(mealHeader);

            const emptySlot = document.createElement('div');
            emptySlot.className = 'empty-slot';
            emptySlot.textContent = 'No meal planned';
            mealSlot.appendChild(emptySlot);

            dayColumn.appendChild(mealSlot);
        });

        calendarGrid.appendChild(dayColumn);
    });

    loadMealsForWeek();
}

function loadMealsForWeek() {
    const userId = localStorage.getItem('userId');
    if (!userId) {
        showMessage('Please log in to view your meal plan', 'error');
        return;
    }

    const weekEnd = new Date(currentWeekStart);
    weekEnd.setDate(weekEnd.getDate() + 6);

    fetch(`${API_BASE_URL}/meal_plans.php?user_id=${userId}&start_date=${currentWeekStart.toISOString()}&end_date=${weekEnd.toISOString()}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                data.meals.forEach(meal => {
                    const mealSlot = document.getElementById(`${meal.day}-${meal.meal_type}`);
                    if (mealSlot) {
                        const emptySlot = mealSlot.querySelector('.empty-slot');
                        if (emptySlot) {
                            emptySlot.remove();
                        }

                        const mealContent = document.createElement('div');
                        mealContent.className = 'meal-content';

                        const mealItem = document.createElement('div');
                        mealItem.className = 'meal-item';

                        const recipeName = document.createElement('span');
                        recipeName.textContent = meal.recipe_name;

                        const deleteBtn = document.createElement('button');
                        deleteBtn.className = 'delete-btn';
                        deleteBtn.innerHTML = '&times;';
                        deleteBtn.onclick = () => removeMealFromPlan(meal.id);

                        mealItem.appendChild(recipeName);
                        mealItem.appendChild(deleteBtn);
                        mealContent.appendChild(mealItem);
                        mealSlot.appendChild(mealContent);
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error loading meals:', error);
            showMessage('Failed to load meal plan', 'error');
        });
}

function removeMealFromPlan(mealId) {
    const userId = localStorage.getItem('userId');
    if (!userId) {
        showMessage('Please log in to modify your meal plan', 'error');
        return;
    }

    fetch(`${API_BASE_URL}/meal_plans.php`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            user_id: userId,
            meal_id: mealId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Meal removed from plan successfully', 'success');
            loadMealsForWeek();
        } else {
            showMessage(data.message || 'Failed to remove meal from plan', 'error');
        }
    })
    .catch(error => {
        console.error('Error removing meal:', error);
        showMessage('Failed to remove meal from plan', 'error');
    });
}

function previousWeek() {
    currentWeekStart.setDate(currentWeekStart.getDate() - 7);
    initializeCalendar();
}

function nextWeek() {
    currentWeekStart.setDate(currentWeekStart.getDate() + 7);
    initializeCalendar();
}

// Initialize calendar when the page loads
document.addEventListener('DOMContentLoaded', () => {
    initializeCalendar();
});
