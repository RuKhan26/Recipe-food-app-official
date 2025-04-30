-- Create recipes table if it doesn't exist
CREATE TABLE IF NOT EXISTS recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    ingredients TEXT NOT NULL,
    instructions TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample recipes
INSERT INTO recipes (name, ingredients, instructions) VALUES
('Classic Spaghetti Bolognese', '500g ground beef, 1 onion, 2 cloves garlic, 400g canned tomatoes, 2 tbsp tomato paste, 1 tsp dried oregano, 400g spaghetti, salt and pepper to taste', '1. Brown the beef in a large pan\n2. Add chopped onion and garlic, cook until soft\n3. Add tomatoes, tomato paste, and oregano\n4. Simmer for 20 minutes\n5. Cook spaghetti according to package instructions\n6. Serve sauce over spaghetti'),
('Chicken Stir Fry', '2 chicken breasts, 2 bell peppers, 1 onion, 2 cups broccoli, 3 tbsp soy sauce, 2 tbsp vegetable oil, 2 cloves garlic, 1 tsp ginger', '1. Cut chicken into bite-sized pieces\n2. Heat oil in a wok or large pan\n3. Stir-fry chicken until cooked through\n4. Add vegetables and stir-fry for 5 minutes\n5. Add soy sauce, garlic, and ginger\n6. Cook for 2 more minutes'),
('Vegetable Soup', '2 carrots, 2 celery stalks, 1 onion, 2 potatoes, 1 can diced tomatoes, 4 cups vegetable broth, 2 cloves garlic, 1 tsp thyme, salt and pepper to taste', '1. Chop all vegetables\n2. Heat oil in a large pot\n3. Sauté onion and garlic\n4. Add remaining vegetables and cook for 5 minutes\n5. Add broth and tomatoes\n6. Simmer for 20 minutes until vegetables are tender'),
('Chocolate Chip Cookies', '2 1/4 cups flour, 1 cup butter, 3/4 cup sugar, 3/4 cup brown sugar, 2 eggs, 1 tsp vanilla, 1 tsp baking soda, 2 cups chocolate chips', '1. Cream butter and sugars\n2. Add eggs and vanilla\n3. Mix in dry ingredients\n4. Fold in chocolate chips\n5. Drop by spoonfuls onto baking sheet\n6. Bake at 350°F for 10-12 minutes'); 