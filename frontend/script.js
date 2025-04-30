// === Logout User ===
function logout() {
  // Clear the stored user data
  localStorage.removeItem('currentUser');
  
  // Update the UI
  const loginStatus = document.getElementById('loginStatus');
  const userDisplay = document.getElementById('userDisplay');
  
  userDisplay.textContent = '';
  loginStatus.style.display = 'none';
  
  // Show welcome modal
  document.getElementById('welcomeModal').style.display = 'block';
  
  // Clear any user-specific data
  document.getElementById('favoriteList').innerHTML = '';
  document.getElementById('journalEntries').innerHTML = '';
  
  alert('Logged out successfully!');
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

  fetch('http://localhost:8000/backend/api/users.php?action=register', {
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

  fetch('http://localhost:8000/backend/api/users.php?action=login', {
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
      
      // Store username in localStorage for persistence
      localStorage.setItem('currentUser', username);
    } else {
      alert('Login failed. Please check your credentials.');
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
  if (currentUser) {
    // User is already logged in
    document.getElementById('welcomeModal').style.display = 'none';
    document.getElementById('loginModal').style.display = 'none';
    document.getElementById('registerModal').style.display = 'none';
    
    const loginStatus = document.getElementById('loginStatus');
    const userDisplay = document.getElementById('userDisplay');
    
    userDisplay.textContent = `Logged in as: ${currentUser}`;
    loginStatus.style.display = 'flex';
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

async function searchRecipes() {
    const ingredients = document.getElementById('ingredients').value;
    if (!ingredients) {
        alert('Please enter ingredients');
        return;
    }

    try {
        showLoadingSpinner();
        const response = await fetch(`${RECIPES_API}?action=ai_search&ingredients=${encodeURIComponent(ingredients)}`);
        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.error || 'Error finding recipes');
        }
        const recipes = await response.json();
        displayRecipes(recipes);
    } catch (error) {
        console.error('Search error:', error);
        alert('Error finding recipes: ' + error.message);
    } finally {
        hideLoadingSpinner();
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
            <button onclick="saveToFavorites(${JSON.stringify(recipe).replace(/"/g, '&quot;')})">
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
  const currentUser = localStorage.getItem('currentUser');
  if (!currentUser) {
    alert('Please log in to save recipes to favorites');
    return;
  }

  fetch('http://localhost:8000/backend/api/favorites.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ 
      recipe_id: recipeId,
      user_id: 1 // For now, using a default user_id
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
    alert(error.message || 'An error occurred while saving the recipe');
  });
}

// === Load Favorites ===
function loadFavorites() {
  const currentUser = localStorage.getItem('currentUser');
  if (!currentUser) {
    document.getElementById('favoriteList').innerHTML = '<p>Please log in to see your favorites</p>';
    return;
  }

  fetch('http://localhost:8000/backend/api/favorites.php?user_id=1') // For now, using a default user_id
    .then(res => {
      if (!res.ok) {
        throw new Error('Failed to load favorites');
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
        <li class="recipe-card">
          <h3>${recipe.name}</h3>
          <p><strong>Ingredients:</strong> ${recipe.ingredients}</p>
          <p><strong>Instructions:</strong> ${recipe.instructions}</p>
        </li>
      `).join('');
    })
    .catch(error => {
      console.error('Error loading favorites:', error);
      document.getElementById('favoriteList').innerHTML = 
        '<p class="error">Error loading favorites. Please try again.</p>';
    });
}

// Load favorites when the page loads
window.addEventListener('load', () => {
  const currentUser = localStorage.getItem('currentUser');
  if (currentUser) {
    loadFavorites();
  }
});

// === Save Journal ===
function saveJournal() {
  const title = document.getElementById('journalTitle').value.trim();
  const content = document.getElementById('journalContent').value.trim();

  if (!title || !content) {
    alert('Please fill in both title and content');
    return;
  }

  fetch('http://localhost:8000/backend/api/journal.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ title, content })
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
    alert(error.message || 'An error occurred while saving the entry');
  });
}

// === Load Journal Entries ===
function loadJournalEntries() {
  fetch('http://localhost:8000/backend/api/journal.php')
    .then(res => {
      if (!res.ok) {
        throw new Error('Failed to load journal entries');
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
        li.innerHTML = `
          <strong>${entry.title}</strong>
          <p>${entry.content}</p>
          <small>Created: ${new Date(entry.created_at).toLocaleString()}</small>
        `;
        list.appendChild(li);
      });
    })
    .catch(error => {
      console.error('Error loading journal entries:', error);
      document.getElementById('journalEntries').innerHTML = 
        '<p class="error">Error loading journal entries. Please try again.</p>';
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
