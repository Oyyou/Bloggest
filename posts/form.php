<?php

class Image
{
    public $id;
    public $value;

    public function __construct($id, $value)
    {
        $this->id = $id;
        $this->value = $value;
    }
}

function utf8ize($d)
{
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = utf8ize($v);
        }
    } else if (is_string($d)) {
        return utf8_encode($d);
    }
    return $d;
}

?>

<script>
    $(document).ready(function() {
        <?php if (isset($componentList)) : ?>
            <?php foreach ($componentList as $component) : ?>

                var data = JSON.parse(`<?php echo json_encode(($component)); ?>`);
                switch (data.type) {
                    case "image":
                        addImageComponent(data);
                        break;
                    case "textarea":
                        addTextareaComponent(data);
                        break;
                    default:
                        console.log("Oop");
                        break;
                }

            <?php endforeach; ?>
        <?php endif; ?>
    })

    const getControlsDiv = (id, type, description) => {

        const getButtonControl = (a, b, f) => {

            const removeButton = document.createElement('div');
            removeButton.className = "button " + b;

            const buttonContent = document.createElement('span');
            buttonContent.innerHTML = a;

            buttonContent.addEventListener('click', (e) => {
                e.stopPropagation();
                f(e.target.parentNode.parentNode.parentNode);
            });

            removeButton.appendChild(buttonContent);
            removeButton.addEventListener('click', (e) => {
                f(e.target.parentNode.parentNode);
            });

            return removeButton;
        }

        const controlsDiv = document.createElement('div');
        controlsDiv.className = "form-controls";

        const controlsHeaderDiv = document.createElement('div');
        controlsHeaderDiv.className = "form-controls-header";

        const headerText = document.createElement('h3');
        headerText.className = "form-controls-title"
        headerText.innerHTML = type;

        const descriptionText = document.createElement('p');
        descriptionText.className = "form-controls-description"
        descriptionText.innerHTML = description;

        controlsHeaderDiv.appendChild(headerText);
        controlsHeaderDiv.appendChild(descriptionText);

        const removeButton = getButtonControl('X', 'remove', (value) => {
            document.getElementById("component-list").removeChild(value);
        })

        const moveUpButton = getButtonControl("↑", "move-up", (value) => {

            const parent = value.parentNode;
            const collection = parent.children;

            let obj = null;
            for (let i = 0; i < collection.length; i++) {
                const current = collection[i];

                if (current.id === value.id) {
                    if (i === 0) {
                        return;
                    } else {
                        break;
                    }
                }

                obj = current;
            }
            if (obj) {
                parent.insertBefore(value, obj);
            }
        });

        const moveDownButton = getButtonControl("↓", "move-down", (value) => {

            const parent = value.parentNode;
            const collection = parent.children;

            let obj = null;
            for (let i = collection.length - 1; i > 0; i--) {
                const current = collection[i];

                if (current.id === value.id) {
                    if (i === collection.length - 1) {
                        return;
                    } else {
                        break;
                    }
                }

                obj = current;
            }
            if (obj) {
                parent.insertBefore(obj, value);
            }
        });

        controlsDiv.appendChild(controlsHeaderDiv);
        controlsDiv.appendChild(moveUpButton);
        controlsDiv.appendChild(moveDownButton);
        controlsDiv.appendChild(removeButton);

        return controlsDiv;
    }

    const getBaseComponent = (dbComponent, id, title, description) => {

        const parentDiv = document.createElement('div');
        parentDiv.className = "form-addtional component";
        parentDiv.id = id;

        controlsDiv = getControlsDiv(id, title, description);

        parentDiv.appendChild(controlsDiv);

        return parentDiv;
    }

    const addSectionComponent = (component) => {

        let id = createUUID();

        const childDiv = document.createElement("div");
        childDiv.className = "component-body"

        const sectionTitle = document.createElement("input");
        sectionTitle.required = 'required';

        const titleLabel = document.createElement('input');
        titleLabel.type = "hidden";
        //textLabel.htmlFor = imageInput.id;
        //textLabel.innerHTML = "Select image";
        titleLabel.style = "display: none;";
        titleLabel.name = "componentItems[]";

        sectionTitle.addEventListener('change', (e) => {
            titleLabel.value = JSON.stringify({
                type: "title",
                value: e.target.value,
                uuid: id,
            });
        });

        const textareaElement = document.createElement("textarea");
        textareaElement.required = 'required';
        //textareaElement.name = "components[]";

        const paragraphLabel = document.createElement('input');
        paragraphLabel.type = "hidden";
        //paragraphLabel.htmlFor = imageInput.id;
        //paragraphLabel.innerHTML = "Select image";
        paragraphLabel.style = "display: none;";
        paragraphLabel.name = "componentItems[]";

        textareaElement.addEventListener('change', (e) => {
            paragraphLabel.value = JSON.stringify({
                type: "body",
                value: e.target.value,
                uuid: id,
            });
        });

        const componentLabel = document.createElement('input');
        componentLabel.type = "hidden";
        //paragraphLabel.htmlFor = imageInput.id;
        //paragraphLabel.innerHTML = "Select image";
        componentLabel.style = "display: none;";
        componentLabel.name = "components[]";
        componentLabel.value = JSON.stringify({
            type: "component",
            value: "",
            uuid: id,
        });

        if (component) {
            textareaElement.innerHTML = component.content;
            titleLabel.value = JSON.stringify({
                type: "header",
                value: component.content,
                uuid: component.uuid,
            });
            paragraphLabel.value = JSON.stringify({
                type: "paragraph",
                value: component.content,
                uuid: component.uuid,
            });
            componentLabel.value = JSON.stringify({
                type: "component",
                value: component.content,
                uuid: component.uuid,
            });
        }

        childDiv.appendChild(sectionTitle);
        childDiv.appendChild(textareaElement);
        childDiv.appendChild(titleLabel);
        childDiv.appendChild(paragraphLabel);
        childDiv.appendChild(componentLabel);

        const parentDiv = getBaseComponent(component, id, "Section component", "Description text");
        parentDiv.appendChild(childDiv);

        document.getElementById("component-list").appendChild(parentDiv);
    };

    const addImageComponent = (component) => {

        let id = createUUID();

        const childDiv = document.createElement("div");
        childDiv.className = "component-body"

        const imageElement = document.createElement("img");

        const imageInput = document.createElement('input');
        imageInput.type = "file";
        imageInput.name = "images[]";
        imageInput.accept = "image/*";
        imageInput.id = "image-" + id;
        //imageInput.style = "display:none;";
        imageInput.required = 'required';

        const imageLabel = document.createElement('input');
        imageLabel.type = "hidden";
        //imageLabel.htmlFor = imageInput.id;
        //imageLabel.innerHTML = "Select image";
        imageLabel.style = "display: none;";
        imageLabel.name = "components[]";

        imageInput.addEventListener('change', (e) => {
            var reader = new FileReader();
            reader.onload = function() {
                imageElement.src = reader.result;
            }

            const file = e.target.files[0];

            reader.readAsDataURL(file);
            imageLabel.value = JSON.stringify({
                type: "image",
                value: file.name,
                uuid: id,
            });
        });

        childDiv.appendChild(imageElement);

        if (component && component.content.length > 0) {
            imageElement.style = "width: 240px; height: 240px;";
            imageElement.src = "/uploads/" + component.content;

            imageLabel.value = JSON.stringify({
                type: "image",
                value: component.content,
                uuid: component.uuid,
            });
        } else {
            // Don't give an option of changing the image on edit
            childDiv.appendChild(imageInput);
        }
        childDiv.appendChild(imageLabel);


        const parentDiv = getBaseComponent(component, id, "Image component", "Description text");
        parentDiv.appendChild(childDiv);

        document.getElementById("component-list").appendChild(parentDiv);
    };

    const addTextareaComponent = (component) => {

        let id = createUUID();

        const childDiv = document.createElement("div");
        childDiv.className = "component-body"

        const textareaElement = document.createElement("textarea");
        textareaElement.required = 'required';
        //textareaElement.name = "components[]";

        const label = document.createElement('input');
        label.type = "hidden";
        //label.htmlFor = imageInput.id;
        //label.innerHTML = "Select image";
        label.style = "display: none;";
        label.name = "components[]";

        textareaElement.addEventListener('change', (e) => {
            label.value = JSON.stringify({
                type: "textarea",
                value: e.target.value,
                uuid: id,
            });
        });

        if (component) {
            textareaElement.innerHTML = component.content;
            label.value = JSON.stringify({
                type: "textarea",
                value: component.content,
                uuid: component.uuid,
            });
        }

        childDiv.appendChild(textareaElement);
        childDiv.appendChild(label);

        const parentDiv = getBaseComponent(component, id, "Textarea component", "Description text");
        parentDiv.appendChild(childDiv);

        document.getElementById("component-list").appendChild(parentDiv);
    };
</script>

<?php if (isset($blogTitle)) : ?>
    <h2 class="post-title">
        <?= $blogTitle ?>
    </h2>
<?php endif; ?>
<div class="blog-form-container">
    <div class="form-side-panel">
        <h3 class="form-side-panel-header">Options</h3>
        <button class="addSection">Add Section</button>
        <button id="addTextarea">Add Textarea</button>
        <button id="addImage">Add Image</button>
    </div>
    <form method="post" class="blog-form" enctype="multipart/form-data">
        <div class="form-body" id="blog-form">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required="required" value="<?= (isset($title) ? $title : "") ?>">

            <label for="shortDescription">Short description:</label>
            <input type="text" id="shortDescription" name="shortDescription" required="required" value="<?= (isset($shortDescription) ? $shortDescription : "") ?>">

            <div id="component-list" class="component-list">

            </div>

            <label for="tags">Tags (split by comma):</label>
            <input type="text" id="tags" name="tags" value="<?= (isset($tags) ? $tags : "") ?>">

            <input id="submit" class="button" type="submit" name="submit" value="Save">
        </div>
    </form>
</div>

<script>
    $(".addSection").click(() => addSectionComponent(null));
    $("#addImage").click(() => addImageComponent(null));
    $("#addTextarea").click(() => addTextareaComponent(null));
</script>