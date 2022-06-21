<?php

namespace App\Model;

use Nette;

final class PostFacade
{
	use Nette\SmartObject;

	private Nette\Database\Explorer $database;

	public function __construct(Nette\Database\Explorer $database)
	{
		$this->database = $database;
	}

	public function findPublishedArticles(int $limit, int $offset): Nette\Database\ResultSet
	{
return $this->database->query(
            '
            SELECT p.id, p.title, p.content, p.views_count, p.created_at, p.status, p.image,c.id AS category_id, c.name AS category_name  FROM posts p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE created_at < ?
            ORDER BY created_at DESC
            LIMIT ?
            OFFSET ?',
            new \DateTime,
            $limit,
            $offset
        );
	}

	public function getPublicArticles()/* metoda*/
	{
		return $this->database
			->table('posts')
			->where('created_at < ', new \DateTime)
			->order('created_at DESC');
	}

	public function getPostById(int $postId)
	{
		$post = $this->database
			->table('posts')
			->get($postId);/* parametry ($postId) */

		return $post;
	}

	public function getComments(int $postId)
	{
		return $this->database
			->table('comments')
			->where('post_id', $postId);
	}

	public function addComment(int $postId, \stdClass $data)
	{

		$this->database->table('comments')->insert([
			'post_id' => $postId,
			'name' => $data->name,
			'email' => $data->email,
			'content' => $data->content,
		]);
	}

	public function editPost(int $postId, array $data)
	{
		$post = $this->database
			->table('posts')
			->get($postId);
		$post->update($data);

		return $post;
	}

	public function insertPost(array $data)
	{
		$post = $this->database
			->table('posts')
			->insert($data);

		return $post;
	}

	public function addView(int $postId)
	{
		$currentViews = $this->database
			->table('posts')
			->get($postId)
			->views_count;
		$currentViews++;


		$data['views_count'] = $currentViews;/*pole -> asociativní pole */
		$this->database
			->table('posts')
			->get($postId)
			->update($data);
	}
	public function updateRating(int $userId, int $postId, int $like)
	{
		$ratingRow = $this->database
			->table('rating')
			->get([
				'user_id' => $userId,
				'post_id' => $postId
			]);

		if ($ratingRow != null) {
			$this->database
				->query(
					'UPDATE rating SET like_value = ? WHERE user_id = ? AND post_id = ?',
					$like,
					$userId,
					$postId

				);
			} else {
				$this->database
					->table('rating')
					->insert([
						'user_id' => $userId,
						'post_id' => $postId,
						'like_value' => $like
					]);
			}
		}

	public function getCategories()
	{
		return $this->database
			->table('categories');
	}
	public function getCategory($categoryId)
	{
		return $this->database
			->table('categories')->get($categoryId);
	}

	public function getPostsByCategoryId(int $categoryId)
	{		
		return $this->database
			->table('posts')
			->where('category_id', $categoryId)
			->order('created_at DESC');
	}

	public function getCategoryName(int $categoryId)
	{
		return $this->database
			->table('categories')
			->where(["id" => $categoryId])
			->fetch()
			->name;
	}
	
	public function getUserRating(int $userId, int $postId,)
	{
		$like = $this->database
			->table('rating')
			->where([
				'user_id' => $userId,
				'post_id' => $postId,
			]);

		if ($like->count() == 0) {
			return null;
		}

        return $like->fetch()->like_value;
	}
	public function deletePost(int $postId)
	{
		
		$this->database
			->table('comments')
			->where(['post_id' => $postId])
			->delete();
		$this->database
			->table('rating')
			->where(['post_id' => $postId]) 
			->delete();
			$this->database
			->table('posts')
			->get($postId)
			->delete();
	}

	public function getPublishedArticlesCount(): int
	{
		return $this->database->fetchField('SELECT COUNT(*) FROM posts WHERE created_at < ?', new \DateTime);
	}
	// nápověda databazový dotaz na tabulku rating

	// nejprve zjistit, zda už řádek existuje restrikce where user_id = $userId a post_id = $postId 
	// na základě výsledku zavolat update nebo insert

	public function getComment(int $commentId)
	{
		return $this->database
			->table('comments')
			->where([
				'id' => $commentId]);
	}

}
/*  1.Controler(Presenter)-Post,Homepage, Sign, Error, Edit
	2.View (*.latte)
	3.Model (PostFacade)
	

	/post/show/
	/edit/create/ 
*/