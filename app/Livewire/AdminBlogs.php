<?php

namespace App\Livewire;

use App\Models\blog_posts;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class AdminBlogs extends Component
{
    use WithFileUploads;

    public $author_name;

    public $blog_headline;

    public $blog_slug;

    public $blog_excerpt;

    public $blog_image;

    public $blog_area;


    // Operational

    public $image_url;

    public $loading_image;



    public function save()
    {



        if($this->author_name && $this->blog_headline && $this->blog_slug && $this->blog_excerpt && $this->blog_image  && $this->blog_area){

            $this->validate([
                'blog_image' => 'image|max:1024', // Image validation (1MB max)
            ]);

            // Store the uploaded image and get the file path
            $imagePath = $this->blog_image->store('blog_images', 'public');

            // Generate the full image_URL to the stored image
            $this->image_url = asset('storage/' . $imagePath);

            blog_posts::create([
                'blog_author' => $this->author_name,
                'blog_title' => $this->blog_headline,
                'blog_link' => $this->blog_slug,
                'blog_excerpt' => $this->blog_excerpt,
                'blog_image' => $this->image_url,
                'blog_text' => $this->blog_area,
                'blog_type' => 'custom',
            ]);




            session()->flash('form_completion_message', 'Blog Post Created Successfully');

            $this->dispatch('alert-manager');


        }else if(!$this->author_name || !$this->blog_headline || !$this->blog_slug || !$this->blog_excerpt || !$this->blog_image || !$this->blog_area){

            session()->flash('form_error_message', 'Please fill all the fields');

            $this->dispatch('alert-manager');

        }

    }

    public function updated($property)
    {
        // $property: The name of the current property that was updated

        if ($property === 'blog_image') {

            $this->loading_image = null;

            $this->dispatch('alert-manager');
        }
    }



    public function updating($property, $value)
    {
        // $property: The name of the current property being updated
        // $value: The value about to be set to the property

        if ($property === 'blog_image') {
            $this->loading_image = "Loading...";

            $this->dispatch('alert-manager');

            $this->dispatch('reinitialize_blog_form');
        }
    }




    public function test_image(){
        dd($this->image_url);
    }


    #[On('updateTextarea')]
    public function updateTextarea($text){

        $this->blog_area = $text;


    }

    public function test_textarea(){

        dd($this->blog_area);


    }


    public function changeThemeMode(){

        if(session('theme_mode') == 'light'){

            session(['theme_mode' => 'dark']);

        }else{


            session(['theme_mode' => 'light']);

        }

        $this->dispatch('alert-manager');

    }

    public function clear_form_completion_message(){

        session()->flash('form_completion_message', null);

        $this->dispatch('alert-manager');

    }


    public function clear_form_error_message(){

        session()->flash('form_error_message', null);

        $this->dispatch('alert-manager');

    }

    public function render()
    {
        return view('livewire.admin-blogs');
    }
}
