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

  fetch(`${USERS_API}?action=register`, {
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
    alert(data.message || 'User registered!');
    if (data.message === 'User registered!') {
      showLoginModal(); // Switch to login form after successful registration
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

  fetch(`${USERS_API}?action=login`, {
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
const API_BASE_URL = 'http://localhost:8000/api';
const RECIPES_API = `${API_BASE_URL}/recipes.php`;
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
            const error = await response.json();
            throw new Error(error.error || 'Error finding recipe');
        }
        const recipe = await response.json();
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
  })
  .catch(error => {
    console.error('Error saving recipe:', error);
    document.getElementById('favoriteList').innerHTML = 
      `<p class="error">Error saving recipe: ${error.message}</p>`;
  });
}

// Add function to delete favorites
function deleteFavorite(favoriteId) {
  if (!confirm('Are you sure you want to remove this recipe from your favorites?')) {
    return;
  }

  const userId = localStorage.getItem('userId');
  if (!userId) {
    alert('Please log in to manage favorites');
    return;
  }

  fetch(`${FAVORITES_API}`, {
    method: 'DELETE',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      user_id: userId,
      favorite_id: favoriteId
    })
  })
  .then(res => {
    if (!res.ok) {
      return res.json().then(data => {
        throw new Error(data.error || 'Failed to delete favorite');
      });
    }
    return res.json();
  })
  .then(data => {
    alert('Recipe removed from favorites');
    loadFavorites(); // Refresh the favorites list
  })
  .catch(error => {
    console.error('Error deleting favorite:', error);
    alert('Error deleting favorite: ' + error.message);
  });
}

// === Load Favorites ===
function loadFavorites() {
  const userId = localStorage.getItem('userId');
  if (!userId) {
    document.getElementById('favoriteList').innerHTML = '<p>Please log in to see your favorites</p>';
    return;
  }

  fetch(`${FAVORITES_API}?user_id=${userId}`)
    .then(res => {
      if (!res.ok) {
        return res.json().then(data => {
          throw new Error(data.error || 'Failed to load favorites');
        });
      }
      return res.json();
    })
    .then(favorites => {
      const list = document.getElementById('favoriteList');
      if (favorites.length === 0) {
        list.innerHTML = '<p>No favorite recipes yet</p>';
        return;
      }

      list.innerHTML = favorites.map(recipe => `
        <div class="recipe-card">
          <h3>${recipe.name}</h3>
          <p><strong>Ingredients:</strong> ${recipe.ingredients}</p>
          <p><strong>Instructions:</strong> ${recipe.instructions}</p>
          <button onclick="deleteFavorite(${recipe.favorite_id})" class="delete-btn">Remove from Favorites</button>
        </div>
      `).join('');
    })
    .catch(error => {
      console.error('Error loading favorites:', error);
      document.getElementById('favoriteList').innerHTML = 
        `<p class="error">Error loading favorites: ${error.message}</p>`;
    });
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
function saveJournalToFavorites(entryId, title, content) {
  const userId = localStorage.getItem('userId');
  if (!userId) {
    alert('Please log in to save recipes to favorites');
    return;
  }

  // Create a new recipe object
  const recipeData = {
    user_id: userId,
    name: title,
    ingredients: content,
    instructions: content,
    source: 'journal'
  };

  fetch(`${FAVORITES_API}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(recipeData)
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
    alert('Recipe saved to favorites!');
    loadFavorites(); // Refresh the favorites list
  })
  .catch(error => {
    console.error('Error saving recipe:', error);
    alert('Error saving recipe: ' + error.message);
  });
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

  fetch('http://localhost:8000/backend/api/ai_assistant.php', {
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

    fetch(`http://localhost:8000/api/meal_plans.php?action=get&user_id=${userId}`)
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.error || 'Failed to load meal plans');
                });
            }
            return response.json();
        })
        .then(meals => {
            const mealPlanContainer = document.getElementById('mealPlanContainer');
            if (!mealPlanContainer) return;

            // Clear existing meals
            mealPlanContainer.innerHTML = '';

            // Create day columns
            const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            days.forEach(day => {
                const dayColumn = document.createElement('div');
                dayColumn.className = 'day-column';
                dayColumn.innerHTML = `<h3>${day}</h3>`;

                // Create meal slots for each day
                const mealTypes = ['Breakfast', 'Lunch', 'Dinner'];
                mealTypes.forEach(mealType => {
                    const mealSlot = document.createElement('div');
                    mealSlot.className = 'meal-slot';
                    mealSlot.innerHTML = `<h4>${mealType}</h4>`;
                    dayColumn.appendChild(mealSlot);
                });

                mealPlanContainer.appendChild(dayColumn);
            });

            // Add meals to their respective slots
            meals.forEach(meal => {
                const dayColumn = Array.from(mealPlanContainer.querySelectorAll('.day-column'))
                    .find(col => col.querySelector('h3').textContent === meal.day);
                
                if (dayColumn) {
                    const mealSlot = Array.from(dayColumn.querySelectorAll('.meal-slot'))
                        .find(slot => slot.querySelector('h4').textContent === meal.meal_type);
                    
                    if (mealSlot) {
                        const mealElement = document.createElement('div');
                        mealElement.className = 'meal-item';
                        mealElement.innerHTML = `
                            <h4>${meal.recipe_name}</h4>
                            <button onclick="deleteMeal(${meal.id})" class="delete-btn">Delete</button>
                        `;
                        mealSlot.appendChild(mealElement);
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error loading meal plans:', error);
            showMessage('Error loading meal plans: ' + error.message, 'error');
        });
}

function addMealToPlan() {
    const userId = localStorage.getItem('userId');
    if (!userId) {
        showMessage('Please log in to add meals to your plan', 'error');
        return;
    }

    const recipeId = document.getElementById('recipeSelect').value;
    const day = document.getElementById('daySelect').value;
    const mealType = document.getElementById('mealTypeSelect').value;

    if (!recipeId) {
        showMessage('Please select a recipe', 'error');
        return;
    }

    fetch(`http://localhost:8000/api/meal_plans.php?action=add`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            user_id: userId,
            recipe_id: recipeId,
            day: day,
            meal_type: mealType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        showMessage('Meal added to plan successfully', 'success');
        loadMealPlans(); // Reload the meal plans
    })
    .catch(error => {
        console.error('Error adding meal to plan:', error);
        showMessage('Error adding meal to plan: ' + error.message, 'error');
    });
}

function deleteMeal(mealId) {
    if (!confirm('Are you sure you want to delete this meal from your plan?')) {
        return;
    }

    fetch(`http://localhost:8000/api/meal_plans.php?action=delete&id=${mealId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            showMessage('Meal removed from plan successfully', 'success');
            loadMealPlans(); // Reload the meal plans after deleting
        })
        .catch(error => {
            console.error('Error deleting meal:', error);
            showMessage('Error deleting meal', 'error');
        });
}

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
        document.getElementById('recipeSelect').innerHTML = '<option value="">Please log in to view your recipes</option>';
        return;
    }

    const select = document.getElementById('recipeSelect');
    select.innerHTML = '<option value="">Pick a recipe from your favorites or journal...</option>';

    // Fetch favorite recipes
    fetch(`${FAVORITES_API}?user_id=${userId}`)
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
                    option.value = favorite.id;
                    option.textContent = favorite.name;
                    favGroup.appendChild(option);
                });
                select.appendChild(favGroup);
            }

            // Fetch journal entries
            return fetch(`${JOURNAL_API}?user_id=${userId}`)
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
                            option.textContent = entry.title;
                            journalGroup.appendChild(option);
                        });
                        select.appendChild(journalGroup);
                    }

                    if ((!favorites || favorites.length === 0) && 
                        (!journalEntries || journalEntries.length === 0)) {
                        const option = document.createElement('option');
                        option.disabled = true;
                        option.textContent = 'No recipes found. Add some to your favorites or journal first.';
                        select.appendChild(option);
                    }
                });
        })
        .catch(error => {
            console.error('Error loading recipes:', error);
            const option = document.createElement('option');
            option.disabled = true;
            option.textContent = 'Error loading recipes. Please try again later.';
            select.appendChild(option);
        });
}

// Load favorites when the page loads
window.addEventListener('load', () => {
  const currentUser = localStorage.getItem('currentUser');
  if (currentUser) {
    loadFavorites();
  }
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
