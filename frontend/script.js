// === Register User ===
function register() {
  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('password').value.trim();

  fetch('http://localhost:8000/backend/api/users.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'register', username, password })
  })
  .then(res => res.json())
  .then(data => alert(data.message || 'User registered!'));
}

// === Login User ===
function login() {
  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('password').value.trim();

  fetch('http://localhost:8000/backend/api/users.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ action: 'login', username, password })
  })
  .then(res => res.json())
  .then(data => alert(data.message || 'Logged in!'));
}

// === Search Recipes ===
function searchRecipes() {
  const ingredients = document.getElementById('ingredients').value.trim();
  if (!ingredients) return;

  fetch('http://localhost:8000/backend/api/recipes.php?ingredients=' + encodeURIComponent(ingredients))
      .then(res => res.json())
      .then(data => {
          const list = document.getElementById('recipeList');
          list.innerHTML = '';
          data.forEach(recipe => {
              const li = document.createElement('li');
              li.innerHTML = `
                  <strong>${recipe.name}</strong><br>
                  Ingredients: ${recipe.ingredients}<br>
                  Instructions: ${recipe.instructions}<br>
                  <button onclick="saveRecipeToFavorites(${recipe.id})">Save to Favorites</button>
              `;
              list.appendChild(li);
          });
      });
}

// === Surprise Recipe ===
function randomRecipe() {
  fetch('http://localhost:8000/backend/api/recipes.php')
      .then(res => res.json())
      .then(data => {
          const recipe = data[Math.floor(Math.random() * data.length)];
          const display = document.getElementById('randomRecipeDisplay');
          display.innerHTML = `
              <strong>${recipe.name}</strong><br>
              Ingredients: ${recipe.ingredients}<br>
              Instructions: ${recipe.instructions}<br>
          `;
      });
}

// Function to handle AI Assistant query
async function askAI() {
  const prompt = document.getElementById('aiPrompt').value.trim();

  if (!prompt) {
    alert('Please enter a prompt!');
    return;
  }

  try {
    const response = await fetch('http://localhost:8000/backend/api/ai_assistant.php?prompt=' + encodeURIComponent(prompt));
    const data = await response.json();

    // Display the AI response
    document.getElementById('aiResponse').innerText = data.ai_response;
  } catch (error) {
    console.error('Error fetching AI response:', error);
  }
}


// === Save to Favorites ===
function saveRecipeToFavorites(recipeId) {
  fetch('http://localhost:8000/backend/api/favorites.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ recipe_id: recipeId })
  })
  .then(res => res.json())
  .then(data => alert(data.message || 'Saved to favorites!'));
}

// === Save Journal ===
function saveJournal() {
  const title = document.getElementById('journalTitle').value.trim();
  const content = document.getElementById('journalContent').value.trim();

  fetch('http://localhost:8000/backend/api/journal.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ title, content })
  })
  .then(res => res.json())
  .then(data => {
      const entry = document.createElement('li');
      entry.innerHTML = `<strong>${title}</strong>: ${content}`;
      document.getElementById('journalEntries').appendChild(entry);
  });
}

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
