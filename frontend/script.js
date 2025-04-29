// Function to handle fetching recipes based on ingredients
async function getRecipes(ingredients) {
    const url = `http://localhost:8000/backend/api/recipes.php?ingredients=${encodeURIComponent(ingredients)}`;
  
    try {
      // Fetch the recipes from the backend API
      const response = await fetch(url);
      
      // Check if the response is ok (status 200)
      if (!response.ok) {
        throw new Error('Failed to fetch recipes');
      }
  
      // Parse the response as JSON
      const data = await response.json();
  
      // Process and display the recipes
      displayRecipes(data);
    } catch (error) {
      console.error('Error fetching recipes:', error);
    }
  }
  
  // Function to get a surprise recipe
  async function getSurpriseRecipe() {
    const url = 'http://localhost:8000/backend/api/recipes.php?surprise=true';
  
    try {
      const response = await fetch(url);
  
      if (!response.ok) {
        throw new Error('Failed to fetch surprise recipe');
      }
  
      const data = await response.json();
  
      // Process and display the surprise recipe
      displayRecipes(data);
    } catch (error) {
      console.error('Error fetching surprise recipe:', error);
    }
  }
  
  // Function to display the list of recipes
  function displayRecipes(recipes) {
    const recipesContainer = document.getElementById('recipes-container');
    recipesContainer.innerHTML = ''; // Clear any previous recipes
  
    // Loop through the recipes and display them
    recipes.forEach(recipe => {
      const recipeElement = document.createElement('div');
      recipeElement.classList.add('recipe');
  
      recipeElement.innerHTML = `
        <h3>${recipe.name}</h3>
        <p>${recipe.description}</p>
        <p><strong>Ingredients:</strong> ${recipe.ingredients.join(', ')}</p>
        <button class="save-button" data-recipe-id="${recipe.id}">Save Recipe</button>
      `;
  
      // Append the recipe element to the container
      recipesContainer.appendChild(recipeElement);
    });
  }
  
  // Function to save a recipe to favorites
  async function saveRecipeToFavorites(recipeId) {
    const url = 'http://localhost:8000/backend/api/favorites.php';
    
    const data = {
      recipe_id: recipeId
    };
  
    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      });
  
      if (!response.ok) {
        throw new Error('Failed to save recipe');
      }
  
      const result = await response.json();
      console.log('Recipe saved to favorites:', result);
    } catch (error) {
      console.error('Error saving recipe:', error);
    }
  }
  
  // Event listener to handle "Search Recipes" form submission
  document.getElementById('ingredients-form').addEventListener('submit', function(event) {
    event.preventDefault();
    
    const ingredientsInput = document.getElementById('ingredients');
    const ingredients = ingredientsInput.value.trim();
    
    if (ingredients) {
      getRecipes(ingredients);
    } else {
      alert('Please enter some ingredients');
    }
  });
  
  // Event listener for the "Surprise Recipe" button
  document.getElementById('surprise-button').addEventListener('click', function() {
    getSurpriseRecipe();
  });
  
  // Event delegation to handle "Save Recipe" button clicks
  document.getElementById('recipes-container').addEventListener('click', function(event) {
    if (event.target.classList.contains('save-button')) {
      const recipeId = event.target.getAttribute('data-recipe-id');
      saveRecipeToFavorites(recipeId);
    }
  });
  