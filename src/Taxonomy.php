<?php namespace Devfactory\Taxonomy;

use Devfactory\Taxonomy\Models\Vocabulary;
use Devfactory\Taxonomy\Models\Term;

class Taxonomy {

  protected $vocabulary;
  protected $term;
  protected $term_relation;

  public function __construct(Vocabulary $vocabulary, Term $term) {
    // Inject required Models
    $this->vocabulary = $vocabulary;
    $this->term = $term;
  }

  /**
   * Create a new Vocabulary with the given name
   *
   * @param string $name
   *  The name of the Vocabulary
   *
   * @return mixed
   *  The Vocabulary object if created, FALSE if error creating,
   *  Exception if the vocabulary name already exists.
   */
  public function createVocabulary($name) {
    if ($this->vocabulary->where('name', $name)->count()) {
      throw new Exceptions\VocabularyExistsException();
    }

		return $this->vocabulary->create(['name' => $name]);
	}

  /**
   * Get a Vocabulary by ID
   *
   * @param int $id
   *  The id of the Vocabulary to fetch
   *
   * @return
   *  The Vocabulary Model object, otherwise NULL
   */
  public function getVocabulary($id) {
    return $this->vocabulary->find($id);
  }

  /**
   * Get a Vocabulary by name
   *
   * @param string $name
   *  The name of the Vocabulary to fetch
   *
   * @return
   *  The Vocabulary Model object, otherwise NULL
   */
  public function getVocabularyByName($name) {
    return $this->vocabulary->where('name', $name)->first();
  }

  /**
   * Get a Vocabulary by name
   *
   * @param string $name
   *  The name of the Vocabulary to fetch
   *
   * @return
   *  The Vocabulary Model object, otherwise NULL
   */
  public function getVocabularyByNameAsArray($name) {
    $vocabulary = $this->vocabulary->where('name', $name)->first();

    if (!is_null($vocabulary)) {
      return $vocabulary->terms->lists('name', 'id');
    }

    return [];
  }

  /**
   * Delete a Vocabulary by ID
   *
   * @param int $id
   *  The ID of the Vocabulary to delete
   *
   * @return bool
   *  TRUE if Vocabulary is deletes, otherwise FALSE
   *
   * @thrown Illuminate\Database\Eloquent\ModelNotFoundException
   */
  public function deleteVocabulary($id) {
    $vocabulary = $this->vocabulary->findOrFail($id);

    return $vocabulary->delete();
  }

  /**
   * Delete a Vocabulary by ID
   *
   * @param int $id
   *  The ID of the Vocabulary to delete
   *
   * @return bool
   *  TRUE if Vocabulary is deletes, otherwise FALSE
   *
   * @thrown Illuminate\Database\Eloquent\ModelNotFoundException
   */
  public function deleteVocabularyByName($name) {
    $vocabulary = $this->vocabulary->where('name', $name)->first();

    if (!is_null($vocabulary)) {
      return $vocabulary->delete();
    }

    return FALSE;
  }

  /**
   * Create a new term in a specific vocabulary
   *
   * @param int $vid
   *  The Vocabulary ID in which to add the term
   *
   * @param string $name
   *  The name of the term
   *
   * @param int $parent
   *  The ID of the parent term if it is a child
   *
   * @param int $weight
   *  The weight of the term in order to sort them inside the Vocabulary
   *
   * @return int
   *  The ID of the created term
   *
   * @thrown Illuminate\Database\Eloquent\ModelNotFoundException
   */
  public function createTerm($vid, $name, $parent = 0, $weight = 0) {
    $vocabulary = $this->vocabulary->findOrFail($vid);

    $term = [
      'name' => $name,
      'vocabulary_id' => $vid,
      'parent' => $parent,
      'weight' => $weight,
    ];

    return $this->term->create($term);
  }

}