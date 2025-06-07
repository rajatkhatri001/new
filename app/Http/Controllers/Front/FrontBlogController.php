<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProudMember;
use App\Models\BlogPage;
use App\Models\Blog;
use App\Models\FranchiseOpportunity;
use App\Models\UpcomingEvents;
use App\Models\SocialMedia;

class FrontBlogController extends Controller
{
    public function index(Request $request) {
        $banner = BlogPage::first();
        $blogdetails = Blog::where('status', 1)->paginate(6);
        $proudmember = ProudMember::where('status', 1)->get();
        $upcomingevents = UpcomingEvents::where('status', 1)->get();
    
        if ($request->ajax()) {
            return view('Front.blog-pagination', compact('blogdetails'))->render();
        }
    
        return view('Front.blog', compact('banner', 'blogdetails', 'proudmember', 'upcomingevents'));
    }
    
    public function details($id = null)
    {
        $banner = BlogPage::first();

        $blogdetails = Blog::find($id);
        $proudmember = ProudMember::where('status', 1)->get();
        
        $upcomingevents = UpcomingEvents::where('status', 1)->get();
        // print_r($upcomingevents->array());exit;

        $frenchiseOpp = FranchiseOpportunity::first();

        $socialmedia = SocialMedia::where('status', 1)->get();

        return view('Front.blog_details',compact('id','banner','blogdetails','proudmember','upcomingevents','frenchiseOpp','socialmedia'));

    }
}
