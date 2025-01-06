<?php
include_once 'includes/header.php';

// Assume $pdo is already instantiated and passed to this script
$book = new Book($pdo);

if (!isset($_SESSION['user_id'])) {
    die("Error: User is not logged in. Please log in to create a book.");
}

$userId = $_SESSION['user_id'];
// Fetch authors, illustrators, genres, series, categories, publishers, age recommendations, and status
$authors = $book->selectAllAuthors();
$illustrators = $book->selectAllIllustrators();
$genres = $book->selectAllGenres();
$series = $book->selectAllSeries();
$categories = $book->selectAllCategories();
$publishers = $book->selectAllPublishers();
$ageRecommendations = $book->selectAllAgeRecommendations();
$statuses = $book->selectAllStatuses(); // Add this method to fetch statuses from the database

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'upload.php'; // Handle the image upload

    $bookData = [
        'book_title' => $_POST['book_title'],
        'book_desc' => $_POST['book_desc'],
        'book_language' => $_POST['book_language'],
        'book_release_date' => $_POST['book_release_date'],
        'book_pages' => $_POST['book_pages'],
        'books_price' => $_POST['books_price'],
        'book_series_fk' => $_POST['book_series_fk'],
        'age_recommendation_fk' => $_POST['age_recommendation_fk'],
        'category_fk' => $_POST['category_fk'],
        'publisher_fk' => $_POST['publisher_fk'],
        'created_by_fk' => $userId, // Add this
        'status_fk' => $_POST['status_fk'],
        'img_url' => isset($_SESSION['uploaded_image']) ? $_SESSION['uploaded_image'] : '',
    ];

    $authors = $_POST['authors'] ?? [];
    $illustrators = $_POST['illustrators'] ?? [];
    $genres = $_POST['genres'] ?? [];

    // Handle on-the-fly author creation
    if (!empty($_POST['new_authors'])) {
        $newAuthors = explode(',', $_POST['new_authors']);
        foreach ($newAuthors as $newAuthor) {
            $newAuthorId = $book->createAuthorOnTheFly(trim($newAuthor));
            if ($newAuthorId) {
                $authors[] = $newAuthorId;
            }
        }
    }

    // Handle on-the-fly illustrator creation
    if (!empty($_POST['new_illustrators'])) {
        $newIllustrators = explode(',', $_POST['new_illustrators']);
        foreach ($newIllustrators as $newIllustrator) {
            $newIllustratorId = $book->createIllustratorOnTheFly(trim($newIllustrator));
            if ($newIllustratorId) {
                $illustrators[] = $newIllustratorId;
            }
        }
    }

    // Handle on-the-fly genre creation
    if (!empty($_POST['new_genres'])) {
        $newGenres = explode(',', $_POST['new_genres']);
        foreach ($newGenres as $newGenre) {
            $newGenreId = $book->createGenreOnTheFly(trim($newGenre));
            if ($newGenreId) {
                $genres[] = $newGenreId;
            }
        }
    }

    // Handle on-the-fly series creation
    if (!empty($_POST['new_series'])) {
        $newSeries = explode(',', $_POST['new_series']);
        foreach ($newSeries as $newSerie) {
            $newSerieId = $book->createSeriesOnTheFly(trim($newSerie));
            if ($newSerieId) {
                $series[] = $newSerieId;
            }
        }
    }

    // Handle on-the-fly age recommendation creation
    if (!empty($_POST['new_age_recommendations'])) {
        $newAgeRecommendations = explode(',', $_POST['new_age_recommendations']);
        foreach ($newAgeRecommendations as $newAgeRecommendation) {
            $newAgeRecommendationId = $book->createAgeRecommendationOnTheFly(trim($newAgeRecommendation));
            if ($newAgeRecommendationId) {
                $ageRecommendations[] = $newAgeRecommendationId;
            }
        }
    }

    // Handle on-the-fly category creation
    if (!empty($_POST['new_categories'])) {
        $newCategories = explode(',', $_POST['new_categories']);
        foreach ($newCategories as $newCategory) {
            $newCategoryId = $book->createCategoryOnTheFly(trim($newCategory));
            if ($newCategoryId) {
                $categories[] = $newCategoryId;
            }
        }
    }

    // Handle on-the-fly publisher creation
    if (!empty($_POST['new_publishers'])) {
        $newPublishers = explode(',', $_POST['new_publishers']);
        foreach ($newPublisher as $newPublisher) {
            $newPublisherId = $book->createPublisherOnTheFly(trim($newPublisher));
            if ($newPublisherId) {
                $publishers[] = $newPublisherId;
            }
        }
    }

    // Handle on-the-fly status creation
    if (!empty($_POST['new_statuses'])) {
        $newStatuses = explode(',', $_POST['new_statuses']);
        foreach ($newStatus as $newStatus) {
            $newStatusId = $book->createStatusOnTheFly(trim($newStatus));
            if ($newStatusId) {
                $statuses[] = $newStatusId;
            }
        }
    }

    if ($book->validateBookData($bookData) && !empty($authors) && !empty($illustrators) && !empty($genres)) {
        $newBookId = $book->createBook($bookData, $authors, $illustrators, $genres);
        if ($newBookId) {
            echo "<div class='alert alert-success'>Book created successfully with ID: $newBookId</div>";
        } else {
            echo "<div class='alert alert-danger'>Failed to create book.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Please fill in all required fields, including authors, illustrators, and genres.</div>";
    }
}

function renderSelect($id, $label, $options, $multiple = false, $required = false, $placeholder = '', $newField = false) {
    $multipleAttr = $multiple ? 'multiple' : '';
    $requiredAttr = $required ? 'required' : '';
    echo "<div class='mb-3'>
            <label for='$id' class='form-label'>$label</label>
            <select class='form-select select2-multiple' style='width: 100%' id='$id' name='{$id}[]' $multipleAttr $requiredAttr>
                <option value=''>$placeholder</option>";
    foreach ($options as $option) {
        $value = htmlspecialchars($option["{$id}_id"]);
        $name = htmlspecialchars($option["{$id}_name"]);
        echo "<option value='$value'>$name</option>";
    }
    echo "</select>";
    if ($newField) {
        echo "<input type='text' class='form-control mt-2' id='new_$id' name='new_$id' placeholder='Add new $id, separated by commas'>
              <button type='button' class='btn btn-secondary mt-2' id='add-$id-btn'>Add $label</button>";
    }
    echo "</div>";
}

?>

<div class="container mt-4">
    <h2>Create a New Book</h2>
    <?php
    if (isset($_SESSION['upload_error'])) {
        echo "<div class='alert alert-danger'>{$_SESSION['upload_error']}</div>";
        unset($_SESSION['upload_error']);
    }
    ?>
    <form method="POST" action="createbook.php" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="book_title" class="form-label">Book Title</label>
            <input type="text" class="form-control" id="book_title" name="book_title" required>
        </div>
        <div class="mb-3">
            <label for="book_desc" class="form-label">Description</label>
            <textarea class="form-control" id="book_desc" name="book_desc" rows="4"></textarea>
        </div>
        <div class="mb-3">
            <label for="book_language" class="form-label">Language</label>
            <input type="text" class="form-control" id="book_language" name="book_language" required>
        </div>
        <div class="mb-3">
            <label for="book_release_date" class="form-label">Release Date</label>
            <input type="date" class="form-control" id="book_release_date" name="book_release_date" required>
        </div>
        <div class="mb-3">
            <label for="book_pages" class="form-label">Pages</label>
            <input type="number" class="form-control" id="book_pages" name="book_pages" required>
        </div>
        <div class="mb-3">
            <label for="books_price" class="form-label">Price</label>
            <input type="number" class="form-control" id="books_price" name="books_price" step="0.01" required>
        </div>
        <?php
        renderSelect('book series', 'Series', $series, false, false, 'Select series', true);
        renderSelect('age recommendation', 'Age Recommendation', $ageRecommendations, false, true, 'Select age recommendation', true);
        renderSelect('category', 'Category', $categories, false, true, 'Select category', true);
        renderSelect('publisher', 'Publisher', $publishers, false, true, 'Select publisher', true);
        renderSelect('status', 'Status', $statuses, false, true, 'Select status', true);
        ?>
        <div class="mb-3">
            <label for="img_url" class="form-label">Image URL</label>
            <input type="file" class="form-control" id="img_url" name="book-img">
        </div>
        <?php
        renderSelect('authors', 'Authors', $authors, true, true, 'Select authors', true);
        renderSelect('illustrators', 'Illustrators', $illustrators, true, true, 'Select illustrators', true);
        renderSelect('genres', 'Genres', $genres, true, true, 'Select genres', true);
        ?>
        <button type="submit" class="btn btn-primary my-3">Create Book</button>
    </form>
</div>

<!-- Include jQuery and Select2 JavaScript and CSS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-multiple').select2({
            placeholder: "Select options",
            allowClear: true
        });

        $('#add-author-btn').on('click', function() {
            var newAuthors = $('#new_authors').val().split(',');
            newAuthors.forEach(function(author) {
                $.ajax({
                    url: 'ajax/create-book-author.php',
                    type: 'POST',
                    data: { authorName: author.trim() },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            var newOption = new Option(author.trim(), data.author_id, false, true);
                            $('#authors').append(newOption).trigger('change');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while adding the author.');
                    }
                });
            });
            $('#new_authors').val('');
        });

        $('#add-illustrator-btn').on('click', function() {
            var newIllustrators = $('#new_illustrators').val().split(',');
            newIllustrators.forEach(function(illustrator) {
                $.ajax({
                    url: 'ajax/create-book-illustrator.php',
                    type: 'POST',
                    data: { illustratorName: illustrator.trim() },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            var newOption = new Option(illustrator.trim(), data.illustrator_id, false, true);
                            $('#illustrators').append(newOption).trigger('change');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while adding the illustrator.');
                    }
                });
            });
            $('#new_illustrators').val('');
        });

        $('#add-genre-btn').on('click', function() {
            var newGenres = $('#new_genres').val().split(',');
            newGenres.forEach(function(genre) {
                $.ajax({
                    url: 'ajax/create-book-genre.php',
                    type: 'POST',
                    data: { genreName: genre.trim() },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            var newOption = new Option(genre.trim(), data.genre_id, false, true);
                            $('#genres').append(newOption).trigger('change');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while adding the genre.');
                    }
                });
            });
            $('#new_genres').val('');
        });

        $('#add-series-btn').on('click', function() {
            var newSeries = $('#new_series').val().split(',');
            newSeries.forEach(function(serie) {
                $.ajax({
                    url: 'ajax/create-book-series.php',
                    type: 'POST',
                    data: { seriesName: serie.trim() },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            var newOption = new Option(serie.trim(), data.series_id, false, true);
                            $('#series').append(newOption).trigger('change');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while adding the series.');
                    }
                });
            });
            $('#new_series').val('');
        });

        $('#add-age-recommendation-btn').on('click', function() {
            var newAgeRecommendations = $('#new_age_recommendations').val().split(',');
            newAgeRecommendations.forEach(function(ageRecommendation) {
                $.ajax({
                    url: 'ajax/create-book-age-recommendation.php',
                    type: 'POST',
                    data: { ageRecommendationName: ageRecommendation.trim() },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            var newOption = new Option(ageRecommendation.trim(), data.age_recommendation_id, false, true);
                            $('#age_recommendations').append(newOption).trigger('change');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while adding the age recommendation.');
                    }
                });
            });
            $('#new_age_recommendations').val('');
        });

        $('#add-category-btn').on('click', function() {
            var newCategories = $('#new_categories').val().split(',');
            newCategories.forEach(function(category) {
                $.ajax({
                    url: 'ajax/create-book-category.php',
                    type: 'POST',
                    data: { categoryName: category.trim() },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            var newOption = new Option(category.trim(), data.category_id, false, true);
                            $('#categories').append(newOption).trigger('change');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while adding the category.');
                    }
                });
            });
            $('#new_categories').val('');
        });

        $('#add-publisher-btn').on('click', function() {
            var newPublishers = $('#new_publishers').val().split(',');
            newPublishers.forEach(function(publisher) {
                $.ajax({
                    url: 'ajax/create-book-publisher.php',
                    type: 'POST',
                    data: { publisherName: publisher.trim() },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            var newOption = new Option(publisher.trim(), data.publisher_id, false, true);
                            $('#publishers').append(newOption).trigger('change');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while adding the publisher.');
                    }
                });
            });
            $('#new_publishers').val('');
        });

        $('#add-status-btn').on('click', function() {
            var newStatuses = $('#new_statuses').val().split(',');
            newStatuses.forEach(function(status) {
                $.ajax({
                    url: 'ajax/create-book-status.php',
                    type: 'POST',
                    data: { statusName: status.trim() },
                    success: function(response) {
                        var data = JSON.parse(response);
                        if (data.status === 'success') {
                            var newOption = new Option(status.trim(), data.status_id, false, true);
                            $('#status_fk').append(newOption).trigger('change');
                        } else {
                            alert('Error: ' + data.message);
                        }
                    },
                    error: function() {
                        alert('An error occurred while adding the status.');
                    }
                });
            });
            $('#new_statuses').val('');
        });
    });
</script>

<?php
include_once 'includes/footer.php';
?>
