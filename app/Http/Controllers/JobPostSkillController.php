namespace App\Http\Controllers;

use App\Models\JobPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobPostSkillController extends Controller
{
    /**
     * Retrieve all skills associated with a specific job post.
     *
     * This method returns a list of skills associated with the specified job post.
     *
     * @param int $jobId - The ID of the job post.
     * @param Request $request - The request object containing query parameters.
     *
     * @return JsonResponse - A JSON response containing skills associated with the job post.
     *
     * Usage example:
     * GET /api/v1/jobs/1/skills?search=java
     * This request will return a list of skills associated with the job post with ID 1,
     * filtered by the search term 'java' in the skill name.
     */
    public function index(int $jobId, Request $request): JsonResponse
    {
        // Retrieve the job post by ID
        $jobPost = JobPost::findOrFail($jobId);

        // Retrieve query parameter for search
        $search = $request->query('search');

        // Create a base query for the job post's skills
        $query = $jobPost->skills();

        // Apply search if a search term is provided
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Get all skills associated with the job post
        $skills = $query->get();

        // Return the skills as a JSON response
        return response()->json($skills);
    }

    /**
     * Attach a skill to a specific job post.
     *
     * This method adds a skill to a job post if the skill is not already attached.
     * It validates the incoming request and ensures that the skill and job post exist.
     *
     * @param Request $request - The request object containing the skill ID.
     * @param int $jobId - The ID of the job post.
     *
     * @return JsonResponse - A JSON response indicating success or error.
     *
     * Usage example:
     * POST /api/v1/jobs/1/skills
     * {
     *     "skill_id": 5
     * }
     * This request will attach the skill with ID 5 to the job post with ID 1 if not already attached.
     */
    public function store(Request $request, int $jobId): JsonResponse
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'skill_id' => 'required|integer|exists:skills,id',
        ]);

        // If validation fails, return a JSON error response
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Retrieve the skill ID from the request
        $skillId = $request->input('skill_id');

        // Retrieve the job post by ID
        $jobPost = JobPost::findOrFail($jobId);

        // Check if the skill is already attached to the job post
        if ($jobPost->skills->contains($skillId)) {
            // Return an error response if the skill is already attached to the job post
            return response()->json(['message' => 'Skill is already attached to the job post.'], 400);
        }

        // Attach the skill to the job post
        $jobPost->skills()->attach($skillId);

        // Return a success response
        return response()->json(['message' => 'Skill attached to job post successfully.'], 201);
    }

    /**
     * Remove a specific skill from a specific job post.
     *
     * This method detaches a skill from a job post if it is currently attached.
     * It validates that both the job post and the skill exist.
     *
     * @param int $jobId - The ID of the job post.
     * @param int $skillId - The ID of the skill to detach from the job post.
     *
     * @return JsonResponse - A JSON response indicating success or error.
     *
     * Usage example:
     * DELETE /api/v1/jobs/1/skills/5
     * This request will detach the skill with ID 5 from the job post with ID 1.
     */
    public function destroy(int $jobId, int $skillId): JsonResponse
    {
        // Retrieve the job post and skill by their IDs
        $jobPost = JobPost::findOrFail($jobId);
        $skill = Skill::findOrFail($skillId);

        // Check if the skill is attached to the job post
        if ($jobPost->skills->contains($skill->id)) {
            // Detach the skill from the job post
            $jobPost->skills()->detach($skill->id);

            // Return a success response
            return response()->json(['message' => 'Skill detached from job post successfully.']);
        } else {
            // Return an error response if the skill was not attached to the job post
            return response()->json(['message' => 'Skill is not attached to the job post.'], 400);
        }
    }
}