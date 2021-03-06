<?php

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

                var uuid = undefined; // data.componentId ? data.uuid : undefined;
                if (data.componentId) {
                    const parentComponent = null;
                }
                var parentId = (data.componentId ? (data.parentUUID + "-component-body") : "component-list");

                var isRequired = data.isRequired === "1";

                switch (data.type) {
                    case "component":
                        addSectionComponent(data, parentId, uuid, isRequired);
                        break;
                    case "section":
                        addSectionComponent(data, parentId, uuid), isRequired;
                        break;
                    case "image":
                        addImageComponent(data, "component-list");
                        break;
                    case "textarea":
                        addTextareaComponent(data, parentId, uuid, isRequired);
                        break;
                    case "title":
                        addTitleComponent(data, parentId, uuid, isRequired);
                        break;
                    default:
                        console.log(data.type);
                        break;
                }

            <?php endforeach; ?>
        <?php endif; ?>
    })

    const getControlsDiv = (id, parentId, type, description, isRequired = false) => {

        const getButtonControl = (text, type, action) => {

            const removeButton = document.createElement('input');
            removeButton.className = "button " + type;
            removeButton.type = "button";
            removeButton.value = text;

            removeButton.addEventListener('click', (e) => {
                action(e.target.parentNode.parentNode);
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
            document.getElementById(parentId).removeChild(value);
        })

        const minimizeButton = getButtonControl('_', "minimize", (value) => {
            $("#" + id + ">.component-main").toggleClass("component-minimized");
        });

        const moveUpButton = getButtonControl("???", "move-up", (value) => {

            const parent = value.parentNode;
            const collection = parent.childNodes;

            let obj = null;
            for (let i = 0; i < collection.length; i++) {
                const current = collection[i];

                if (!current.classList.contains("component")) {
                    continue;
                }

                if (current.hasAttribute("can-edit")) {
                    if (current.getAttribute("can-edit") === "false") {
                        continue;
                    }
                }

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

        const moveDownButton = getButtonControl("???", "move-down", (value) => {

            const parent = value.parentNode;
            const collection = parent.children;

            let obj = null;
            for (let i = collection.length - 1; i > 0; i--) {
                const current = collection[i];

                if (!current.classList.contains("component")) {
                    continue;
                }

                if (current.hasAttribute("can-edit")) {
                    if (current.getAttribute("can-edit") === "false") {
                        continue;
                    }
                }

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
        controlsDiv.appendChild(minimizeButton);

        if (isRequired === false) {
            controlsDiv.appendChild(moveUpButton);
            controlsDiv.appendChild(moveDownButton);
            controlsDiv.appendChild(removeButton);
        }

        return controlsDiv;
    }

    const getBaseComponent = (dbComponent, id, parentId, title, description, isRequired = false) => {

        const parentDiv = document.createElement('div');
        parentDiv.className = "component" + (isRequired ? " component-required" : "");
        parentDiv.id = id;

        parentDiv.setAttribute("can-edit", !isRequired);

        controlsDiv = getControlsDiv(id, parentId, title, description, isRequired);

        const main = document.createElement("div");
        main.id = id + "-component-main";
        main.className = "component-main"

        const head = document.createElement("div");
        head.id = id + "-component-head";
        head.className = "component-head"

        const body = document.createElement("div");
        body.id = id + "-component-body";
        body.className = "component-body"

        main.appendChild(head);
        main.appendChild(body);

        parentDiv.appendChild(controlsDiv);
        parentDiv.appendChild(main);

        return parentDiv;
    }

    const addComponent = (args = {
        dbComponent: null,
        parentId: "",
        parentUUID: null,
        isRequired: false
    }) => {

        const {
            dbComponent,
            parentId,
            parentUUID,
            isRequired
        } = args;

        let id = dbComponent ? dbComponent.uuid : createUUID();

        const componentLabel = document.createElement('input');
        componentLabel.type = "hidden";
        componentLabel.style = "display: none;";
        componentLabel.name = "componentItems[]";
        componentLabel.value = JSON.stringify({
            type: "component",
            value: "",
            parentUUID: parentUUID,
            uuid: id,
        });

        if (dbComponent) {
            componentLabel.value = JSON.stringify({
                type: "component",
                value: "",
                parentUUID: parentUUID,
                uuid: id,
            });
        }

        const parentDiv = getBaseComponent(dbComponent, id, parentId, "Section component", "Description text", isRequired);
        document.getElementById(parentId).appendChild(parentDiv);

        const body = document.getElementById(id + "-component-body");
        body.appendChild(componentLabel);
    }

    const addSectionComponent = (dbComponent, parentId, parentUUID, isRequired = false) => {
        
        let id = dbComponent ? dbComponent.uuid : createUUID();

        addComponent({
            dbComponent: {
                uuid: id
            },
            parentId: parentId,
            isRequired: isRequired
        });

        addParagraphButton = document.createElement("input");
        addParagraphButton.type = "button";
        addParagraphButton.value = "Add Paragraph";

        addParagraphButton.addEventListener('click', (e) => {
            addTextareaComponent(dbComponent, id + "-component-body", id);
        });

        document.getElementById(id + "-component-head").appendChild(addParagraphButton);

        if (!dbComponent) {
            addTitleComponent(dbComponent, id + "-component-body", id, true);
            addTextareaComponent(dbComponent, id + "-component-body", id, true);
        }
    };

    const addImageComponent = (component, parentId, parentUUID) => {

        let id = component ? component.uuid : createUUID();

        const childDiv = document.createElement("div");
        childDiv.id = id + "-component-body";
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


        const parentDiv = getBaseComponent(component, id, parentId, "Image component", "Description text", isRequired);
        parentDiv.appendChild(childDiv);

        document.getElementById(parentId).appendChild(parentDiv);
    };

    const addTextareaComponent = (dbComponent, parentId, parentUUID, isRequired = false) => {

        let id = dbComponent ? dbComponent.uuid : createUUID();

        const textareaElement = document.createElement("textarea");
        textareaElement.required = 'required';

        const label = document.createElement('input');
        label.type = "hidden";
        label.style = "display: none;";
        label.name = "componentItems[]";

        textareaElement.addEventListener('change', (e) => {
            label.value = JSON.stringify({
                type: "textarea",
                value: e.target.value,
                parentUUID: parentUUID,
                uuid: id,
                isRequired: isRequired,
            });
        });

        if (dbComponent) {
            textareaElement.innerHTML = dbComponent.content;
            label.value = JSON.stringify({
                type: "textarea",
                value: dbComponent.content,
                parentUUID: parentUUID,
                uuid: dbComponent.uuid,
                isRequired: isRequired,
            });
        }

        const parentDiv = getBaseComponent(dbComponent, id, parentId, "Paragraph", "", isRequired);
        document.getElementById(parentId).appendChild(parentDiv);

        const body = document.getElementById(id + "-component-body");
        body.appendChild(textareaElement);
        body.appendChild(label);

    };

    const addTitleComponent = (dbComponent, parentId, parentUUID, isRequired = false) => {

        let id = dbComponent ? dbComponent.uuid : createUUID();

        const sectionTitle = document.createElement("input");
        sectionTitle.required = 'required';

        const label = document.createElement('input');
        label.type = "hidden";
        label.style = "display: none;";
        label.name = "componentItems[]";

        sectionTitle.addEventListener('change', (e) => {
            label.value = JSON.stringify({
                type: "title",
                value: e.target.value,
                parentUUID: parentUUID,
                uuid: id,
                isRequired: isRequired,
            });
        });

        if (dbComponent) {
            sectionTitle.value = dbComponent.content;
            label.value = JSON.stringify({
                type: "title",
                value: dbComponent.content,
                parentUUID: parentUUID,
                uuid: dbComponent.uuid,
                isRequired: isRequired,
            });
        }


        const parentDiv = getBaseComponent(dbComponent, id, parentId, "Title", "", isRequired);
        document.getElementById(parentId).appendChild(parentDiv);

        const body = document.getElementById(id + "-component-body");
        body.appendChild(sectionTitle);
        body.appendChild(label);
    }
</script>

<?php if (isset($blogTitle)) : ?>
    <h2 class="post-title">
        <?= $blogTitle ?>
    </h2>
<?php endif; ?>
<div class="blog-form-container">
    <fieldset class="form-side-panel">
        <legend class="form-side-panel-header">Options</legend>
        <button class="addSection">Add Section</button>
        <button id="addTextarea">Add Textarea</button>
        <button id="addImage">Add Image</button>
    </fieldset>
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

            <button id="submit" class="button" type="submit" name="submit">Save</button>
        </div>
    </form>
</div>

<script>
    $(".addSection").click(() => addSectionComponent(null, "component-list"));
    $("#addImage").click(() => addImageComponent(null, "component-list"));
    $("#addTextarea").click(() => addTextareaComponent(null, "component-list"));
</script>